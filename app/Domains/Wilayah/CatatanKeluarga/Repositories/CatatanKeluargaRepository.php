<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Repositories;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWargaAnggota;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CatatanKeluargaRepository implements CatatanKeluargaRepositoryInterface
{
    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        $kegiatanByNama = DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->pluck('aktivitas', 'kegiatan');

        $aktivitasLabel = static function (Collection $items, string $kegiatan): string {
            return (bool) $items->get($kegiatan, false) ? 'Ya' : 'Tidak';
        };

        return DataWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get()
            ->values()
            ->map(fn (DataWarga $item, int $index) => [
                'id' => $item->id,
                'nomor_urut' => $index + 1,
                'nama_kepala_rumah_tangga' => $item->nama_kepala_keluarga,
                'jumlah_anggota_rumah_tangga' => $item->total_warga,
                'kerja_bakti' => $aktivitasLabel($kegiatanByNama, 'Kerja Bakti'),
                'rukun_kematian' => $aktivitasLabel($kegiatanByNama, 'Rukun Kematian'),
                'kegiatan_keagamaan' => $aktivitasLabel($kegiatanByNama, 'Kegiatan Keagamaan'),
                'jimpitan' => $aktivitasLabel($kegiatanByNama, 'Jimpitan'),
                'arisan' => $aktivitasLabel($kegiatanByNama, 'Arisan'),
                'lain_lain' => $aktivitasLabel($kegiatanByNama, 'Lain-Lain'),
                'keterangan' => $item->keterangan,
            ]);
    }

    public function getRekapDasaWismaByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);

        return $households
            ->values()
            ->map(function (DataWarga $item, int $index) use ($activityFlags): array {
                $metrics = $this->buildHouseholdMetrics($item, $activityFlags);

                return array_merge([
                    'id' => $item->id,
                    'nomor_urut' => $index + 1,
                    'nama_kepala_rumah_tangga' => $item->nama_kepala_keluarga,
                    'jml_kk' => 1,
                    'ket' => $item->keterangan,
                ], $metrics);
            });
    }

    public function getRekapIbuHamilDasaWismaByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);

        return $households
            ->values()
            ->map(function (DataWarga $item, int $index): array {
                $anggota = $item->relationLoaded('anggota') ? $item->anggota : collect();
                $ibu = $this->findIbuCandidate($anggota);
                $bayi = $this->findBayiCandidate($anggota);
                $statusIbu = $this->extractMaternalStatus($item, $ibu);
                $deathInfo = $this->extractDeathInfo($item, $ibu, $bayi);

                return [
                    'nomor_urut' => $index + 1,
                    'kelompok_dasawisma' => $this->normalizeDasaWismaName($item->dasawisma),
                    'kelompok_pkk_rt' => $this->extractRtNumber($item),
                    'kelompok_pkk_rw' => $this->extractRwNumber($item),
                    'dusun_lingkungan' => $this->extractDusunLingkunganName($item),
                    'desa_kelurahan' => $this->extractDesaKelurahanNameOrFallback($item),
                    'nama_ibu' => $ibu?->nama ?? '-',
                    'nama_suami' => trim((string) $item->nama_kepala_keluarga) !== '' ? (string) $item->nama_kepala_keluarga : '-',
                    'status_ibu' => $statusIbu,
                    'nama_bayi' => $bayi?->nama ?? '-',
                    'kelahiran_l' => $bayi && $bayi->jenis_kelamin === 'L' ? 1 : 0,
                    'kelahiran_p' => $bayi && $bayi->jenis_kelamin === 'P' ? 1 : 0,
                    'tanggal_lahir' => $bayi?->tanggal_lahir?->format('Y-m-d') ?? '-',
                    'akta_ada' => 0,
                    'akta_tidak_ada' => $bayi ? 1 : 0,
                    'catatan_kematian_nama' => $deathInfo['nama'],
                    'catatan_kematian_status' => $deathInfo['status'],
                    'kematian_l' => $deathInfo['l'],
                    'kematian_p' => $deathInfo['p'],
                    'tanggal_meninggal' => $deathInfo['tanggal'],
                    'sebab_meninggal' => $deathInfo['sebab'],
                    'keterangan' => trim((string) $item->keterangan) !== '' ? (string) $item->keterangan : '-',
                ];
            });
    }

    public function getRekapIbuHamilPkkRtByLevelAndArea(string $level, int $areaId): Collection
    {
        $rows = $this->getRekapIbuHamilDasaWismaByLevelAndArea($level, $areaId);
        $grouped = $rows->groupBy(
            fn (array $item): string => trim((string) ($item['kelompok_dasawisma'] ?? '-')) !== ''
                ? trim((string) ($item['kelompok_dasawisma'] ?? '-'))
                : '-'
        );

        $result = collect();

        foreach ($grouped as $namaKelompokDasaWisma => $groupItems) {
            $groupItems = $groupItems->values();
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value): bool => is_string($value) && trim($value) !== '' && trim($value) !== '-')
                ->unique()
                ->implode('; ');

            $result->push([
                'nomor_urut' => $result->count() + 1,
                'nama_kelompok_dasa_wisma' => $namaKelompokDasaWisma,
                'jumlah_ibu_hamil' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'HAMIL'),
                'jumlah_ibu_melahirkan' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'MELAHIRKAN'),
                'jumlah_ibu_nifas' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'NIFAS'),
                'jumlah_ibu_meninggal' => $this->countArrayItemsByValue($groupItems, 'catatan_kematian_status', 'IBU'),
                'jumlah_bayi_lahir_l' => $this->sumArrayIntField($groupItems, 'kelahiran_l'),
                'jumlah_bayi_lahir_p' => $this->sumArrayIntField($groupItems, 'kelahiran_p'),
                'jumlah_akte_kelahiran_ada' => $this->sumArrayIntField($groupItems, 'akta_ada'),
                'jumlah_akte_kelahiran_tidak_ada' => $this->sumArrayIntField($groupItems, 'akta_tidak_ada'),
                'jumlah_bayi_meninggal_l' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BAYI')
                    ->sum(fn (array $item): int => (int) ($item['kematian_l'] ?? 0)),
                'jumlah_bayi_meninggal_p' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BAYI')
                    ->sum(fn (array $item): int => (int) ($item['kematian_p'] ?? 0)),
                'jumlah_balita_meninggal_l' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BALITA')
                    ->sum(fn (array $item): int => (int) ($item['kematian_l'] ?? 0)),
                'jumlah_balita_meninggal_p' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BALITA')
                    ->sum(fn (array $item): int => (int) ($item['kematian_p'] ?? 0)),
                'keterangan' => $keterangan !== '' ? $keterangan : '-',
                'rt_rw_dus_ling' => $this->composeRtRwDusLingLabel($groupItems),
                'desa_kelurahan' => $this->composeArrayFieldLabel($groupItems, 'desa_kelurahan'),
            ]);
        }

        return $result;
    }

    public function getRekapIbuHamilPkkRwByLevelAndArea(string $level, int $areaId): Collection
    {
        $rows = $this->getRekapIbuHamilDasaWismaByLevelAndArea($level, $areaId);
        $grouped = $rows->groupBy(
            fn (array $item): string => trim((string) ($item['kelompok_pkk_rt'] ?? '-')) !== ''
                ? trim((string) ($item['kelompok_pkk_rt'] ?? '-'))
                : '-'
        );

        $sortedRtNumbers = $grouped
            ->keys()
            ->sort(fn (string $left, string $right): int => $this->compareRtNumbers($left, $right))
            ->values();

        $result = collect();

        foreach ($sortedRtNumbers as $rtNumber) {
            $groupItems = $grouped->get($rtNumber, collect())->values();
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value): bool => is_string($value) && trim($value) !== '' && trim($value) !== '-')
                ->unique()
                ->implode('; ');

            $result->push([
                'nomor_urut' => $result->count() + 1,
                'nomor_rt' => $rtNumber,
                'jumlah_kelompok_dasawisma' => $groupItems
                    ->map(fn (array $item): string => trim((string) ($item['kelompok_dasawisma'] ?? '-')))
                    ->filter(fn (string $value): bool => $value !== '' && $value !== '-')
                    ->unique()
                    ->count(),
                'jumlah_ibu_hamil' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'HAMIL'),
                'jumlah_ibu_melahirkan' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'MELAHIRKAN'),
                'jumlah_ibu_nifas' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'NIFAS'),
                'jumlah_ibu_meninggal' => $this->countArrayItemsByValue($groupItems, 'catatan_kematian_status', 'IBU'),
                'jumlah_bayi_lahir_l' => $this->sumArrayIntField($groupItems, 'kelahiran_l'),
                'jumlah_bayi_lahir_p' => $this->sumArrayIntField($groupItems, 'kelahiran_p'),
                'jumlah_akte_kelahiran_ada' => $this->sumArrayIntField($groupItems, 'akta_ada'),
                'jumlah_akte_kelahiran_tidak_ada' => $this->sumArrayIntField($groupItems, 'akta_tidak_ada'),
                'jumlah_bayi_meninggal_l' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BAYI')
                    ->sum(fn (array $item): int => (int) ($item['kematian_l'] ?? 0)),
                'jumlah_bayi_meninggal_p' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BAYI')
                    ->sum(fn (array $item): int => (int) ($item['kematian_p'] ?? 0)),
                'jumlah_balita_meninggal_l' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BALITA')
                    ->sum(fn (array $item): int => (int) ($item['kematian_l'] ?? 0)),
                'jumlah_balita_meninggal_p' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BALITA')
                    ->sum(fn (array $item): int => (int) ($item['kematian_p'] ?? 0)),
                'keterangan' => $keterangan !== '' ? $keterangan : '-',
                'nomor_rw' => $this->composeArrayFieldLabel($groupItems, 'kelompok_pkk_rw'),
                'dusun_lingkungan' => $this->composeArrayFieldLabel($groupItems, 'dusun_lingkungan'),
                'desa_kelurahan' => $this->composeArrayFieldLabel($groupItems, 'desa_kelurahan'),
            ]);
        }

        return $result;
    }

    public function getRekapIbuHamilPkkDusunLingkunganByLevelAndArea(string $level, int $areaId): Collection
    {
        $rows = $this->getRekapIbuHamilDasaWismaByLevelAndArea($level, $areaId);
        $grouped = $rows->groupBy(
            fn (array $item): string => trim((string) ($item['kelompok_pkk_rw'] ?? '-')) !== ''
                ? trim((string) ($item['kelompok_pkk_rw'] ?? '-'))
                : '-'
        );

        $sortedRwNumbers = $grouped
            ->keys()
            ->sort(fn (string $left, string $right): int => $this->compareRwNumbers($left, $right))
            ->values();

        $result = collect();

        foreach ($sortedRwNumbers as $rwNumber) {
            $groupItems = $grouped->get($rwNumber, collect())->values();
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value): bool => is_string($value) && trim($value) !== '' && trim($value) !== '-')
                ->unique()
                ->implode('; ');
            $groupByRt = $groupItems->groupBy(
                fn (array $item): string => trim((string) ($item['kelompok_pkk_rt'] ?? '-'))
            );

            $result->push([
                'nomor_urut' => $result->count() + 1,
                'nomor_rw' => $rwNumber,
                'jumlah_rt' => $groupItems
                    ->map(fn (array $item): string => trim((string) ($item['kelompok_pkk_rt'] ?? '-')))
                    ->filter(fn (string $value): bool => $value !== '' && $value !== '-')
                    ->unique()
                    ->count(),
                // Kolom 4.18d mengikuti cara pengisian: penjumlahan dari buku tingkat PKK RW per RT.
                'jumlah_kelompok_dasawisma' => (int) $groupByRt
                    ->reject(fn (Collection $items, string $rt): bool => $rt === '' || $rt === '-')
                    ->sum(function (Collection $items): int {
                        return $items
                            ->map(fn (array $item): string => trim((string) ($item['kelompok_dasawisma'] ?? '-')))
                            ->filter(fn (string $value): bool => $value !== '' && $value !== '-')
                            ->unique()
                            ->count();
                    }),
                'jumlah_ibu_hamil' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'HAMIL'),
                'jumlah_ibu_melahirkan' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'MELAHIRKAN'),
                'jumlah_ibu_nifas' => $this->countArrayItemsByValue($groupItems, 'status_ibu', 'NIFAS'),
                'jumlah_ibu_meninggal' => $this->countArrayItemsByValue($groupItems, 'catatan_kematian_status', 'IBU'),
                'jumlah_bayi_lahir_l' => $this->sumArrayIntField($groupItems, 'kelahiran_l'),
                'jumlah_bayi_lahir_p' => $this->sumArrayIntField($groupItems, 'kelahiran_p'),
                'jumlah_akte_kelahiran_ada' => $this->sumArrayIntField($groupItems, 'akta_ada'),
                'jumlah_akte_kelahiran_tidak_ada' => $this->sumArrayIntField($groupItems, 'akta_tidak_ada'),
                'jumlah_bayi_meninggal_l' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BAYI')
                    ->sum(fn (array $item): int => (int) ($item['kematian_l'] ?? 0)),
                'jumlah_bayi_meninggal_p' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BAYI')
                    ->sum(fn (array $item): int => (int) ($item['kematian_p'] ?? 0)),
                'jumlah_balita_meninggal_l' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BALITA')
                    ->sum(fn (array $item): int => (int) ($item['kematian_l'] ?? 0)),
                'jumlah_balita_meninggal_p' => (int) $groupItems
                    ->filter(fn (array $item): bool => ($item['catatan_kematian_status'] ?? '-') === 'BALITA')
                    ->sum(fn (array $item): int => (int) ($item['kematian_p'] ?? 0)),
                'keterangan' => $keterangan !== '' ? $keterangan : '-',
                'dusun_lingkungan' => $this->composeArrayFieldLabel($groupItems, 'dusun_lingkungan'),
                'desa_kelurahan' => $this->composeArrayFieldLabel($groupItems, 'desa_kelurahan'),
            ]);
        }

        return $result;
    }

    public function getRekapIbuHamilTpPkkDesaKelurahanByLevelAndArea(string $level, int $areaId): Collection
    {
        $rows = $this->getRekapIbuHamilPkkDusunLingkunganByLevelAndArea($level, $areaId);
        $grouped = $rows->groupBy(function (array $item): string {
            $dusunLingkungan = trim((string) ($item['dusun_lingkungan'] ?? '-'));
            $desaKelurahan = trim((string) ($item['desa_kelurahan'] ?? '-'));

            $normalizedDusunLingkungan = $dusunLingkungan !== '' ? $dusunLingkungan : '-';
            $normalizedDesaKelurahan = $desaKelurahan !== '' ? $desaKelurahan : '-';

            return sprintf('%s::%s', $normalizedDesaKelurahan, $normalizedDusunLingkungan);
        });

        $sortedGroupKeys = $grouped
            ->keys()
            ->sort(fn (string $left, string $right): int => strnatcasecmp($left, $right))
            ->values();

        $result = collect();

        foreach ($sortedGroupKeys as $groupKey) {
            $groupItems = $grouped->get($groupKey, collect());
            $groupItems = $groupItems->values();

            [$namaDesaKelurahan, $namaDusunLingkungan] = array_pad(explode('::', $groupKey, 2), 2, '-');

            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value): bool => is_string($value) && trim($value) !== '' && trim($value) !== '-')
                ->unique()
                ->implode('; ');

            $result->push([
                'nomor_urut' => $result->count() + 1,
                'nama_dusun_lingkungan' => $namaDusunLingkungan,
                'desa_kelurahan' => $namaDesaKelurahan,
                'jumlah_rw' => $groupItems
                    ->map(fn (array $item): string => trim((string) ($item['nomor_rw'] ?? '-')))
                    ->filter(fn (string $value): bool => $value !== '' && $value !== '-')
                    ->unique()
                    ->count(),
                'jumlah_rt' => $this->sumArrayIntField($groupItems, 'jumlah_rt'),
                'jumlah_kelompok_dasawisma' => $this->sumArrayIntField($groupItems, 'jumlah_kelompok_dasawisma'),
                'jumlah_ibu_hamil' => $this->sumArrayIntField($groupItems, 'jumlah_ibu_hamil'),
                'jumlah_ibu_melahirkan' => $this->sumArrayIntField($groupItems, 'jumlah_ibu_melahirkan'),
                'jumlah_ibu_nifas' => $this->sumArrayIntField($groupItems, 'jumlah_ibu_nifas'),
                'jumlah_ibu_meninggal' => $this->sumArrayIntField($groupItems, 'jumlah_ibu_meninggal'),
                'jumlah_bayi_lahir_l' => $this->sumArrayIntField($groupItems, 'jumlah_bayi_lahir_l'),
                'jumlah_bayi_lahir_p' => $this->sumArrayIntField($groupItems, 'jumlah_bayi_lahir_p'),
                'jumlah_akte_kelahiran_ada' => $this->sumArrayIntField($groupItems, 'jumlah_akte_kelahiran_ada'),
                'jumlah_akte_kelahiran_tidak_ada' => $this->sumArrayIntField($groupItems, 'jumlah_akte_kelahiran_tidak_ada'),
                'jumlah_bayi_meninggal_l' => $this->sumArrayIntField($groupItems, 'jumlah_bayi_meninggal_l'),
                'jumlah_bayi_meninggal_p' => $this->sumArrayIntField($groupItems, 'jumlah_bayi_meninggal_p'),
                'jumlah_balita_meninggal_l' => $this->sumArrayIntField($groupItems, 'jumlah_balita_meninggal_l'),
                'jumlah_balita_meninggal_p' => $this->sumArrayIntField($groupItems, 'jumlah_balita_meninggal_p'),
                'keterangan' => $keterangan !== '' ? $keterangan : '-',
            ]);
        }

        return $result;
    }

    public function getRekapIbuHamilTpPkkKecamatanByLevelAndArea(string $level, int $areaId): Collection
    {
        $rows = $this->getRekapIbuHamilTpPkkDesaKelurahanByLevelAndArea($level, $areaId);
        $grouped = $rows->groupBy(fn (array $item): string => trim((string) ($item['desa_kelurahan'] ?? '-')) !== ''
            ? trim((string) ($item['desa_kelurahan'] ?? '-'))
            : '-');

        $sortedDesaKelurahanNames = $grouped
            ->keys()
            ->sort(fn (string $left, string $right): int => strnatcasecmp($left, $right))
            ->values();

        $result = collect();

        foreach ($sortedDesaKelurahanNames as $desaKelurahanName) {
            $groupItems = $grouped->get($desaKelurahanName, collect())->values();
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value): bool => is_string($value) && trim($value) !== '' && trim($value) !== '-')
                ->unique()
                ->implode('; ');

            $result->push([
                'nomor_urut' => $result->count() + 1,
                'nama_desa_kelurahan' => $desaKelurahanName,
                'jumlah_dusun_lingkungan' => $groupItems
                    ->map(fn (array $item): string => trim((string) ($item['nama_dusun_lingkungan'] ?? '-')))
                    ->filter(fn (string $value): bool => $value !== '' && $value !== '-')
                    ->unique()
                    ->count(),
                'jumlah_rw' => $this->sumArrayIntField($groupItems, 'jumlah_rw'),
                'jumlah_rt' => $this->sumArrayIntField($groupItems, 'jumlah_rt'),
                'jumlah_kelompok_dasawisma' => $this->sumArrayIntField($groupItems, 'jumlah_kelompok_dasawisma'),
                'jumlah_ibu_hamil' => $this->sumArrayIntField($groupItems, 'jumlah_ibu_hamil'),
                'jumlah_ibu_melahirkan' => $this->sumArrayIntField($groupItems, 'jumlah_ibu_melahirkan'),
                'jumlah_ibu_nifas' => $this->sumArrayIntField($groupItems, 'jumlah_ibu_nifas'),
                'jumlah_ibu_meninggal' => $this->sumArrayIntField($groupItems, 'jumlah_ibu_meninggal'),
                'jumlah_bayi_lahir_l' => $this->sumArrayIntField($groupItems, 'jumlah_bayi_lahir_l'),
                'jumlah_bayi_lahir_p' => $this->sumArrayIntField($groupItems, 'jumlah_bayi_lahir_p'),
                'jumlah_akte_kelahiran_ada' => $this->sumArrayIntField($groupItems, 'jumlah_akte_kelahiran_ada'),
                'jumlah_akte_kelahiran_tidak_ada' => $this->sumArrayIntField($groupItems, 'jumlah_akte_kelahiran_tidak_ada'),
                'jumlah_bayi_meninggal_l' => $this->sumArrayIntField($groupItems, 'jumlah_bayi_meninggal_l'),
                'jumlah_bayi_meninggal_p' => $this->sumArrayIntField($groupItems, 'jumlah_bayi_meninggal_p'),
                'jumlah_balita_meninggal_l' => $this->sumArrayIntField($groupItems, 'jumlah_balita_meninggal_l'),
                'jumlah_balita_meninggal_p' => $this->sumArrayIntField($groupItems, 'jumlah_balita_meninggal_p'),
                'keterangan' => $keterangan !== '' ? $keterangan : '-',
            ]);
        }

        return $result;
    }

    public function getRekapPkkRtByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $grouped = $households->groupBy(
            fn (DataWarga $item): string => $this->normalizeDasaWismaName($item->dasawisma)
        );

        $rows = collect();

        foreach ($grouped as $namaDasaWisma => $groupItems) {
            $metrics = $this->sumHouseholdMetrics($groupItems, $activityFlags);
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                ->unique()
                ->implode('; ');

            $rows->push(array_merge([
                'nomor_urut' => $rows->count() + 1,
                'nama_dasawisma' => $namaDasaWisma,
                'jml_krt' => $groupItems->count(),
                'jml_kk' => $groupItems->count(),
                'ket' => $keterangan !== '' ? $keterangan : null,
            ], $metrics));
        }

        return $rows;
    }

    public function getCatatanPkkRwByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $grouped = $households->groupBy(
            fn (DataWarga $item): string => $this->extractRtNumber($item)
        );

        $sortedRtNumbers = $grouped
            ->keys()
            ->sort(fn (string $left, string $right): int => $this->compareRtNumbers($left, $right))
            ->values();

        $rows = collect();

        foreach ($sortedRtNumbers as $rtNumber) {
            $groupItems = $grouped->get($rtNumber, collect());
            $metrics = $this->sumHouseholdMetrics($groupItems, $activityFlags);
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                ->unique()
                ->implode('; ');

            $rows->push(array_merge([
                'nomor_urut' => $rows->count() + 1,
                'nomor_rt' => $rtNumber,
                'jml_dasawisma' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->normalizeDasaWismaName($item->dasawisma))
                    ->unique()
                    ->count(),
                'jml_krt' => $groupItems->count(),
                'jml_kk' => $groupItems->count(),
                'ket' => $keterangan !== '' ? $keterangan : null,
            ], $metrics, [
                'tiga_buta_l' => 0,
                'tiga_buta_p' => 0,
                'sungai' => 0,
                'jumlah_sarana_mck' => (int) ($metrics['memiliki_mck_septic'] ?? 0),
            ]));
        }

        return $rows;
    }

    public function getRekapRwByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $grouped = $households->groupBy(
            fn (DataWarga $item): string => $this->extractRwNumber($item)
        );

        $sortedRwNumbers = $grouped
            ->keys()
            ->sort(fn (string $left, string $right): int => $this->compareRwNumbers($left, $right))
            ->values();

        $rows = collect();

        foreach ($sortedRwNumbers as $rwNumber) {
            $groupItems = $grouped->get($rwNumber, collect());
            $metrics = $this->sumHouseholdMetrics($groupItems, $activityFlags);
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                ->unique()
                ->implode('; ');

            $rows->push(array_merge([
                'nomor_urut' => $rows->count() + 1,
                'nomor_rw' => $rwNumber,
                'jml_rt' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRtNumber($item))
                    ->filter(fn (string $rt): bool => $rt !== '-')
                    ->unique()
                    ->count(),
                'jml_dasawisma' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->normalizeDasaWismaName($item->dasawisma))
                    ->unique()
                    ->count(),
                'jml_krt' => $groupItems->count(),
                'jml_kk' => $groupItems->count(),
                'ket' => $keterangan !== '' ? $keterangan : null,
            ], $metrics, [
                'tiga_buta_l' => 0,
                'tiga_buta_p' => 0,
                'sungai' => 0,
                'jumlah_sarana_mck' => (int) ($metrics['memiliki_mck_septic'] ?? 0),
            ]));
        }

        return $rows;
    }

    public function getCatatanTpPkkDesaKelurahanByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $grouped = $households->groupBy(
            fn (DataWarga $item): string => $this->extractDusunLingkunganName($item)
        );

        $rows = collect();

        foreach ($grouped as $namaDusunLingkungan => $groupItems) {
            $metrics = $this->sumHouseholdMetrics($groupItems, $activityFlags);
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                ->unique()
                ->implode('; ');

            $rows->push(array_merge([
                'nomor_urut' => $rows->count() + 1,
                'nama_dusun_lingkungan' => $namaDusunLingkungan,
                'jml_rw' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRwNumber($item))
                    ->filter(fn (string $rw): bool => $rw !== '-')
                    ->unique()
                    ->count(),
                'jml_rt' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRtNumber($item))
                    ->filter(fn (string $rt): bool => $rt !== '-')
                    ->unique()
                    ->count(),
                'jml_dasawisma' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->normalizeDasaWismaName($item->dasawisma))
                    ->unique()
                    ->count(),
                'jml_krt' => $groupItems->count(),
                'jml_kk' => $groupItems->count(),
                'ket' => $keterangan !== '' ? $keterangan : null,
            ], $metrics, [
                'sungai' => 0,
                'jumlah_sarana_mck' => (int) ($metrics['memiliki_mck_septic'] ?? 0),
            ]));
        }

        return $rows;
    }

    public function getCatatanTpPkkKecamatanByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $grouped = $households->groupBy(
            fn (DataWarga $item): string => $this->extractDesaKelurahanName($item)
        );

        $rows = collect();

        foreach ($grouped as $namaDesaKelurahan => $groupItems) {
            $metrics = $this->sumHouseholdMetrics($groupItems, $activityFlags);
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                ->unique()
                ->implode('; ');

            $rows->push(array_merge([
                'nomor_urut' => $rows->count() + 1,
                'nama_desa_kelurahan' => $namaDesaKelurahan,
                'jml_dusun_lingkungan' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractDusunLingkunganName($item))
                    ->filter(fn (string $value): bool => $value !== '-')
                    ->unique()
                    ->count(),
                'jml_rw' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRwNumber($item))
                    ->filter(fn (string $rw): bool => $rw !== '-')
                    ->unique()
                    ->count(),
                'jml_rt' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRtNumber($item))
                    ->filter(fn (string $rt): bool => $rt !== '-')
                    ->unique()
                    ->count(),
                'jml_dasawisma' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->normalizeDasaWismaName($item->dasawisma))
                    ->unique()
                    ->count(),
                'jml_krt' => $groupItems->count(),
                'jml_kk' => $groupItems->count(),
                'ket' => $keterangan !== '' ? $keterangan : null,
            ], $metrics, [
                'tiga_buta_l' => 0,
                'tiga_buta_p' => 0,
                'sungai' => 0,
                'jumlah_sarana_mck' => (int) ($metrics['memiliki_mck_septic'] ?? 0),
            ]));
        }

        return $rows;
    }

    public function getCatatanTpPkkKabupatenKotaByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $grouped = $households->groupBy(
            fn (DataWarga $item): string => $this->extractKecamatanName($item)
        );

        $rows = collect();

        foreach ($grouped as $namaKecamatan => $groupItems) {
            $metrics = $this->sumHouseholdMetrics($groupItems, $activityFlags);
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                ->unique()
                ->implode('; ');

            $rows->push(array_merge([
                'nomor_urut' => $rows->count() + 1,
                'nama_kecamatan' => $namaKecamatan,
                'jml_desa_kelurahan' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractDesaKelurahanNameOrFallback($item))
                    ->filter(fn (string $value): bool => $value !== '-')
                    ->unique()
                    ->count(),
                'jml_dusun_lingkungan' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractDusunLingkunganName($item))
                    ->filter(fn (string $value): bool => $value !== '-')
                    ->unique()
                    ->count(),
                'jml_rw' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRwNumber($item))
                    ->filter(fn (string $rw): bool => $rw !== '-')
                    ->unique()
                    ->count(),
                'jml_rt' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRtNumber($item))
                    ->filter(fn (string $rt): bool => $rt !== '-')
                    ->unique()
                    ->count(),
                'jml_dasawisma' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->normalizeDasaWismaName($item->dasawisma))
                    ->unique()
                    ->count(),
                'jml_krt' => $groupItems->count(),
                'jml_kk' => $groupItems->count(),
                'ket' => $keterangan !== '' ? $keterangan : null,
            ], $metrics, [
                'tiga_buta_l' => 0,
                'tiga_buta_p' => 0,
                'sungai' => 0,
                'jumlah_sarana_mck' => (int) ($metrics['memiliki_mck_septic'] ?? 0),
            ]));
        }

        return $rows;
    }

    public function getCatatanTpPkkProvinsiByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $grouped = $households->groupBy(
            fn (DataWarga $item): string => $this->extractKabKotaName($item)
        );

        $rows = collect();

        foreach ($grouped as $namaKabKota => $groupItems) {
            $metrics = $this->sumHouseholdMetrics($groupItems, $activityFlags);
            $keterangan = $groupItems
                ->pluck('keterangan')
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                ->unique()
                ->implode('; ');

            $rows->push(array_merge([
                'nomor_urut' => $rows->count() + 1,
                'nama_kab_kota' => $namaKabKota,
                'jml_kecamatan' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractKecamatanName($item))
                    ->filter(fn (string $value): bool => $value !== '-')
                    ->unique()
                    ->count(),
                'jml_desa_kelurahan' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractDesaKelurahanNameOrFallback($item))
                    ->filter(fn (string $value): bool => $value !== '-')
                    ->unique()
                    ->count(),
                'jml_dusun_lingkungan' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractDusunLingkunganName($item))
                    ->filter(fn (string $value): bool => $value !== '-')
                    ->unique()
                    ->count(),
                'jml_rw' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRwNumber($item))
                    ->filter(fn (string $rw): bool => $rw !== '-')
                    ->unique()
                    ->count(),
                'jml_rt' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->extractRtNumber($item))
                    ->filter(fn (string $rt): bool => $rt !== '-')
                    ->unique()
                    ->count(),
                'jml_dasawisma' => $groupItems
                    ->map(fn (DataWarga $item): string => $this->normalizeDasaWismaName($item->dasawisma))
                    ->unique()
                    ->count(),
                'jml_krt' => $groupItems->count(),
                'jml_kk' => $groupItems->count(),
                'ket' => $keterangan !== '' ? $keterangan : null,
            ], $metrics, [
                'tiga_buta_l' => 0,
                'tiga_buta_p' => 0,
                'sungai' => 0,
                'jumlah_sarana_mck' => (int) ($metrics['memiliki_mck_septic'] ?? 0),
            ]));
        }

        return $rows;
    }

    private function scopedHouseholds(string $level, int $areaId): Collection
    {
        return DataWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->with(['anggota', 'area.parent'])
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array{up2k: bool, pemanfaatan_tanah_pekarangan: bool, industri_rumah_tangga: bool, kesehatan_lingkungan: bool}
     */
    private function buildActivityFlags(string $level, int $areaId): array
    {
        $activityByName = DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->pluck('aktivitas', 'kegiatan');

        return [
            'up2k' => (bool) $activityByName->get('Penghayatan dan Pengamalan Pancasila', false),
            'pemanfaatan_tanah_pekarangan' => DataPemanfaatanTanahPekaranganHatinyaPkk::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->exists(),
            'industri_rumah_tangga' => DataIndustriRumahTangga::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->exists(),
            'kesehatan_lingkungan' => (bool) $activityByName->get('Kerja Bakti', false),
        ];
    }

    /**
     * @param array{up2k: bool, pemanfaatan_tanah_pekarangan: bool, industri_rumah_tangga: bool, kesehatan_lingkungan: bool} $activityFlags
     * @return array<string, int>
     */
    private function buildHouseholdMetrics(DataWarga $item, array $activityFlags): array
    {
        $metrics = $this->emptyRekapMetrics();
        $anggota = $item->relationLoaded('anggota') ? $item->anggota : collect();

        if ($anggota->isEmpty()) {
            $metrics['total_l'] = (int) $item->jumlah_warga_laki_laki;
            $metrics['total_p'] = (int) $item->jumlah_warga_perempuan;
        } else {
            $metrics['total_l'] = $anggota->where('jenis_kelamin', 'L')->count();
            $metrics['total_p'] = $anggota->where('jenis_kelamin', 'P')->count();
            $metrics['balita_l'] = $anggota
                ->where('jenis_kelamin', 'L')
                ->filter(fn (DataWargaAnggota $anggotaItem): bool => $this->isBalita($anggotaItem->umur_tahun))
                ->count();
            $metrics['balita_p'] = $anggota
                ->where('jenis_kelamin', 'P')
                ->filter(fn (DataWargaAnggota $anggotaItem): bool => $this->isBalita($anggotaItem->umur_tahun))
                ->count();
            $metrics['pus'] = $anggota
                ->filter(fn (DataWargaAnggota $anggotaItem): bool => $this->isPusCandidate($anggotaItem))
                ->count();
            $metrics['wus'] = $anggota
                ->where('jenis_kelamin', 'P')
                ->filter(fn (DataWargaAnggota $anggotaItem): bool => $this->isUsiaSubur($anggotaItem->umur_tahun))
                ->count();
            $metrics['lansia'] = $anggota
                ->filter(fn (DataWargaAnggota $anggotaItem): bool => $this->isLansia($anggotaItem->umur_tahun))
                ->count();
        }

        // Indikator kegiatan 4.16 saat ini bersumber dari modul level area (belum per keluarga).
        $metrics['up2k'] = $activityFlags['up2k'] ? 1 : 0;
        $metrics['pemanfaatan_tanah_pekarangan'] = $activityFlags['pemanfaatan_tanah_pekarangan'] ? 1 : 0;
        $metrics['industri_rumah_tangga'] = $activityFlags['industri_rumah_tangga'] ? 1 : 0;
        $metrics['kesehatan_lingkungan'] = $activityFlags['kesehatan_lingkungan'] ? 1 : 0;

        return $metrics;
    }

    /**
     * @param Collection<int, DataWarga> $groupItems
     * @param array{up2k: bool, pemanfaatan_tanah_pekarangan: bool, industri_rumah_tangga: bool, kesehatan_lingkungan: bool} $activityFlags
     * @return array<string, int>
     */
    private function sumHouseholdMetrics(Collection $groupItems, array $activityFlags): array
    {
        $sums = $this->emptyRekapMetrics();

        foreach ($groupItems as $item) {
            $metrics = $this->buildHouseholdMetrics($item, $activityFlags);

            foreach ($metrics as $key => $value) {
                $sums[$key] += (int) $value;
            }
        }

        return $sums;
    }

    /**
     * @return array<string, int>
     */
    private function emptyRekapMetrics(): array
    {
        return [
            'total_l' => 0,
            'total_p' => 0,
            'balita_l' => 0,
            'balita_p' => 0,
            'pus' => 0,
            'wus' => 0,
            'ibu_hamil' => 0,
            'ibu_menyusui' => 0,
            'lansia' => 0,
            'tiga_buta' => 0,
            'berkebutuhan_khusus' => 0,
            'sehat_layak_huni' => 0,
            'tidak_sehat_layak_huni' => 0,
            'memiliki_tempat_sampah' => 0,
            'memiliki_spal' => 0,
            'memiliki_mck_septic' => 0,
            'pdam' => 0,
            'sumur' => 0,
            'dll' => 0,
            'beras' => 0,
            'non_beras' => 0,
            'up2k' => 0,
            'pemanfaatan_tanah_pekarangan' => 0,
            'industri_rumah_tangga' => 0,
            'kesehatan_lingkungan' => 0,
        ];
    }

    private function normalizeDasaWismaName(?string $rawName): string
    {
        $trimmed = trim((string) $rawName);

        return $trimmed !== '' ? $trimmed : '-';
    }

    private function extractRtNumber(DataWarga $item): string
    {
        $sources = [$item->alamat, $item->dasawisma];

        foreach ($sources as $source) {
            $normalized = trim((string) $source);

            if ($normalized === '') {
                continue;
            }

            [$rt,] = $this->extractRtRwPair($normalized);
            if ($rt !== null) {
                return $rt;
            }

            if (preg_match('/\bRT(?:\/RW)?\s*[:.\-]?\s*0*(\d{1,3})\b/i', $normalized, $matches) === 1) {
                return str_pad((string) ((int) $matches[1]), 2, '0', STR_PAD_LEFT);
            }
        }

        return '-';
    }

    private function extractRwNumber(DataWarga $item): string
    {
        $sources = [$item->alamat, $item->dasawisma];

        foreach ($sources as $source) {
            $normalized = trim((string) $source);

            if ($normalized === '') {
                continue;
            }

            [, $rw] = $this->extractRtRwPair($normalized);
            if ($rw !== null) {
                return $rw;
            }

            if (preg_match('/\bRW\s*[:.\-]?\s*0*(\d{1,3})\b/i', $normalized, $matches) === 1) {
                return str_pad((string) ((int) $matches[1]), 2, '0', STR_PAD_LEFT);
            }
        }

        return '-';
    }

    private function extractDusunLingkunganName(DataWarga $item): string
    {
        $sources = [$item->alamat, $item->dasawisma];

        foreach ($sources as $source) {
            $normalized = trim((string) $source);

            if ($normalized === '') {
                continue;
            }

            if (preg_match('/\b(DUSUN|LINGKUNGAN)\s+([^,;]+?)(?=\s+RT\b|\s+RW\b|$)/i', $normalized, $matches) === 1) {
                $prefix = strtoupper(trim((string) $matches[1]));
                $name = trim((string) $matches[2]);
                if ($name !== '') {
                    return sprintf('%s %s', $prefix, $name);
                }
            }
        }

        return $this->normalizeDasaWismaName($item->dasawisma);
    }

    private function extractDesaKelurahanName(DataWarga $item): string
    {
        $sources = [$item->alamat, $item->dasawisma];

        foreach ($sources as $source) {
            $normalized = trim((string) $source);

            if ($normalized === '') {
                continue;
            }

            if (preg_match('/\b(DESA|KELURAHAN|KEL\.?)\s+([^,;]+?)(?=\s+DUSUN\b|\s+LINGKUNGAN\b|\s+RT\b|\s+RW\b|$)/i', $normalized, $matches) === 1) {
                $prefix = strtoupper(str_replace('.', '', trim((string) $matches[1])));
                $name = trim((string) $matches[2]);

                if ($name !== '') {
                    return sprintf('%s %s', $prefix, $name);
                }
            }
        }

        return '-';
    }

    private function extractDesaKelurahanNameOrFallback(DataWarga $item): string
    {
        $extracted = $this->extractDesaKelurahanName($item);

        if ($extracted !== '-') {
            return $extracted;
        }

        $area = $item->relationLoaded('area') ? $item->area : null;

        if ($area?->level === 'desa' && is_string($area->name) && trim($area->name) !== '') {
            return sprintf('DESA %s', trim($area->name));
        }

        return '-';
    }

    private function extractKecamatanName(DataWarga $item): string
    {
        $sources = [$item->alamat, $item->dasawisma];

        foreach ($sources as $source) {
            $normalized = trim((string) $source);

            if ($normalized === '') {
                continue;
            }

            if (preg_match('/\bKECAMATAN\s+([^,;]+?)(?=\s+DESA\b|\s+KELURAHAN\b|\s+KEL\.?\b|\s+DUSUN\b|\s+LINGKUNGAN\b|\s+RT\b|\s+RW\b|$)/i', $normalized, $matches) === 1) {
                $name = trim((string) $matches[1]);
                if ($name !== '') {
                    return $name;
                }
            }
        }

        $area = $item->relationLoaded('area') ? $item->area : null;

        if ($area?->level === 'kecamatan' && is_string($area->name) && trim($area->name) !== '') {
            return trim($area->name);
        }

        if ($area?->level === 'desa' && $area->parent && is_string($area->parent->name) && trim($area->parent->name) !== '') {
            return trim($area->parent->name);
        }

        return '-';
    }

    private function extractKabKotaName(DataWarga $item): string
    {
        $sources = [$item->alamat, $item->dasawisma];

        foreach ($sources as $source) {
            $normalized = trim((string) $source);

            if ($normalized === '') {
                continue;
            }

            if (preg_match('/\b(KAB(?:UPATEN)?|KOTA)\s+([^,;]+?)(?=\s+KECAMATAN\b|\s+DESA\b|\s+KELURAHAN\b|\s+KEL\.?\b|\s+DUSUN\b|\s+LINGKUNGAN\b|\s+RT\b|\s+RW\b|$)/i', $normalized, $matches) === 1) {
                $prefixRaw = strtoupper(trim((string) $matches[1]));
                $prefix = $prefixRaw === 'KABUPATEN' ? 'KAB' : $prefixRaw;
                $name = trim((string) $matches[2]);

                if ($name !== '') {
                    return sprintf('%s %s', $prefix, $name);
                }
            }
        }

        return '-';
    }

    /**
     * @param Collection<int, mixed> $anggota
     */
    private function findIbuCandidate(Collection $anggota): ?DataWargaAnggota
    {
        $perempuan = $anggota
            ->filter(fn ($item): bool => $item instanceof DataWargaAnggota && $item->jenis_kelamin === 'P')
            ->values();

        if ($perempuan->isEmpty()) {
            return null;
        }

        $menikah = $perempuan
            ->filter(fn (DataWargaAnggota $item): bool => $this->containsAnyKeyword($item->status_perkawinan, ['kawin', 'nikah']))
            ->values();

        if ($menikah->isNotEmpty()) {
            return $menikah->sortBy('nomor_urut')->first();
        }

        return $perempuan->sortBy('nomor_urut')->first();
    }

    /**
     * @param Collection<int, mixed> $anggota
     */
    private function findBayiCandidate(Collection $anggota): ?DataWargaAnggota
    {
        $bayi = $anggota
            ->filter(fn ($item): bool => $item instanceof DataWargaAnggota && is_int($item->umur_tahun) && $item->umur_tahun <= 1)
            ->values();

        if ($bayi->isEmpty()) {
            return null;
        }

        return $bayi
            ->sortBy(fn (DataWargaAnggota $item): string => sprintf('%03d-%03d', (int) $item->umur_tahun, (int) $item->nomor_urut))
            ->first();
    }

    private function extractMaternalStatus(DataWarga $household, ?DataWargaAnggota $ibu): string
    {
        $text = Str::lower(trim(implode(' ', array_filter([
            (string) ($household->keterangan ?? ''),
            (string) ($ibu?->keterangan ?? ''),
        ]))));

        if ($text === '') {
            return '-';
        }

        if (str_contains($text, 'melahirkan')) {
            return 'MELAHIRKAN';
        }

        if (str_contains($text, 'nifas')) {
            return 'NIFAS';
        }

        if (str_contains($text, 'hamil')) {
            return 'HAMIL';
        }

        return '-';
    }

    /**
     * @return array{nama: string, status: string, l: int, p: int, tanggal: string, sebab: string}
     */
    private function extractDeathInfo(DataWarga $household, ?DataWargaAnggota $ibu, ?DataWargaAnggota $bayi): array
    {
        $text = Str::lower(trim(implode(' ', array_filter([
            (string) ($household->keterangan ?? ''),
            (string) ($ibu?->keterangan ?? ''),
            (string) ($bayi?->keterangan ?? ''),
        ]))));

        $default = [
            'nama' => '-',
            'status' => '-',
            'l' => 0,
            'p' => 0,
            'tanggal' => '-',
            'sebab' => '-',
        ];

        if ($text === '' || ! str_contains($text, 'meninggal')) {
            return $default;
        }

        if (preg_match('/karena\s+([^.]+)/i', $text, $matches) === 1) {
            $default['sebab'] = trim((string) $matches[1]) !== '' ? trim((string) $matches[1]) : '-';
        }

        if (str_contains($text, 'ibu meninggal')) {
            $default['nama'] = $ibu?->nama ?? '-';
            $default['status'] = 'IBU';
            $default['p'] = 1;

            return $default;
        }

        if (str_contains($text, 'balita meninggal')) {
            $default['nama'] = $bayi?->nama ?? '-';
            $default['status'] = 'BALITA';
            $default['l'] = $bayi?->jenis_kelamin === 'L' ? 1 : 0;
            $default['p'] = $bayi?->jenis_kelamin === 'P' ? 1 : 0;

            return $default;
        }

        if (str_contains($text, 'bayi meninggal')) {
            $default['nama'] = $bayi?->nama ?? '-';
            $default['status'] = 'BAYI';
            $default['l'] = $bayi?->jenis_kelamin === 'L' ? 1 : 0;
            $default['p'] = $bayi?->jenis_kelamin === 'P' ? 1 : 0;
        }

        return $default;
    }

    /**
     * @param string[] $keywords
     */
    private function containsAnyKeyword(?string $value, array $keywords): bool
    {
        $normalized = Str::lower(trim((string) $value));

        if ($normalized === '') {
            return false;
        }

        foreach ($keywords as $keyword) {
            if (str_contains($normalized, Str::lower($keyword))) {
                return true;
            }
        }

        return false;
    }

    private function compareRtNumbers(string $left, string $right): int
    {
        if ($left === $right) {
            return 0;
        }

        if ($left === '-') {
            return 1;
        }

        if ($right === '-') {
            return -1;
        }

        return ((int) $left) <=> ((int) $right);
    }

    private function compareRwNumbers(string $left, string $right): int
    {
        if ($left === $right) {
            return 0;
        }

        if ($left === '-') {
            return 1;
        }

        if ($right === '-') {
            return -1;
        }

        return ((int) $left) <=> ((int) $right);
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    private function extractRtRwPair(string $value): array
    {
        if (preg_match('/\bRT\s*\/\s*RW\s*[:.\-]?\s*0*(\d{1,3})\s*\/\s*0*(\d{1,3})\b/i', $value, $matches) !== 1) {
            return [null, null];
        }

        return [
            str_pad((string) ((int) $matches[1]), 2, '0', STR_PAD_LEFT),
            str_pad((string) ((int) $matches[2]), 2, '0', STR_PAD_LEFT),
        ];
    }

    private function isBalita(?int $umur): bool
    {
        return is_int($umur) && $umur >= 0 && $umur <= 5;
    }

    private function isUsiaSubur(?int $umur): bool
    {
        return is_int($umur) && $umur >= 15 && $umur <= 49;
    }

    private function isLansia(?int $umur): bool
    {
        return is_int($umur) && $umur >= 60;
    }

    private function isPusCandidate(DataWargaAnggota $item): bool
    {
        if (! $this->isUsiaSubur($item->umur_tahun)) {
            return false;
        }

        $status = Str::lower(trim((string) $item->status_perkawinan));

        return $status !== '' && (str_contains($status, 'kawin') || str_contains($status, 'nikah'));
    }

    /**
     * @param Collection<int, array<string, mixed>> $items
     */
    private function countArrayItemsByValue(Collection $items, string $key, string $value): int
    {
        return $items->filter(
            fn (array $item): bool => strtoupper(trim((string) ($item[$key] ?? '-'))) === strtoupper($value)
        )->count();
    }

    /**
     * @param Collection<int, array<string, mixed>> $items
     */
    private function sumArrayIntField(Collection $items, string $key): int
    {
        return (int) $items->sum(
            fn (array $item): int => (int) ($item[$key] ?? 0)
        );
    }

    /**
     * @param Collection<int, array<string, mixed>> $items
     */
    private function composeArrayFieldLabel(Collection $items, string $key): string
    {
        $values = $items
            ->map(fn (array $item): string => trim((string) ($item[$key] ?? '-')))
            ->filter(fn (string $value): bool => $value !== '' && $value !== '-')
            ->unique()
            ->values();

        if ($values->isEmpty()) {
            return '-';
        }

        if ($values->count() === 1) {
            return (string) $values->first();
        }

        return 'MULTI: '.$values->implode(', ');
    }

    /**
     * @param Collection<int, array<string, mixed>> $items
     */
    private function composeRtRwDusLingLabel(Collection $items): string
    {
        $rt = $this->composeArrayFieldLabel($items, 'kelompok_pkk_rt');
        $rw = $this->composeArrayFieldLabel($items, 'kelompok_pkk_rw');
        $dusun = $this->composeArrayFieldLabel($items, 'dusun_lingkungan');

        $rtRw = '-';
        if ($rt !== '-' || $rw !== '-') {
            $rtRw = sprintf('RT %s / RW %s', $rt, $rw);
        }

        if ($rtRw === '-' && $dusun === '-') {
            return '-';
        }

        if ($dusun === '-') {
            return $rtRw;
        }

        if ($rtRw === '-') {
            return $dusun;
        }

        return sprintf('%s / %s', $rtRw, $dusun);
    }
}
