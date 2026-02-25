<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Repositories;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWargaAnggota;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CatatanKeluargaRepository implements CatatanKeluargaRepositoryInterface
{
    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        $kegiatanByNama = DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->pluck('aktivitas', 'kegiatan');

        $activityLabel = static function (Collection $items, string $kegiatan): string {
            return (bool) $items->get($kegiatan, false) ? 'Ya' : 'Tidak';
        };

        return DataWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->through(function (DataWarga $item, int $index) use ($activityLabel, $kegiatanByNama): array {
                return [
                    'id' => $item->id,
                    'nomor_urut' => $index + 1,
                    'nama_kepala_rumah_tangga' => $item->nama_kepala_keluarga,
                    'jumlah_anggota_rumah_tangga' => $item->total_warga,
                    'kerja_bakti' => $activityLabel($kegiatanByNama, 'Kerja Bakti'),
                    'rukun_kematian' => $activityLabel($kegiatanByNama, 'Rukun Kematian'),
                    'kegiatan_keagamaan' => $activityLabel($kegiatanByNama, 'Kegiatan Keagamaan'),
                    'jimpitan' => $activityLabel($kegiatanByNama, 'Jimpitan'),
                    'arisan' => $activityLabel($kegiatanByNama, 'Arisan'),
                    'lain_lain' => $activityLabel($kegiatanByNama, 'Lain-Lain'),
                    'keterangan' => $item->keterangan,
                ];
            })
            ->withQueryString();
    }

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

    public function getDataUmumPkkByLevelAndArea(string $level, int $areaId): Collection
    {
        $households = DataWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->with('anggota')
            ->orderBy('id')
            ->get();

        $grouped = [];

        $ensureRow = function (string $label) use (&$grouped): void {
            if (isset($grouped[$label])) {
                return;
            }

            $grouped[$label] = [
                'pkk_rw' => [],
                'pkk_rt' => [],
                'dasa_wisma' => [],
                'krt' => 0,
                'kk' => 0,
                'jiwa_l' => 0,
                'jiwa_p' => 0,
                'anggota_tp_pkk_l' => 0,
                'anggota_tp_pkk_p' => 0,
                'kader_umum_l' => 0,
                'kader_umum_p' => 0,
                'kader_khusus_l' => 0,
                'kader_khusus_p' => 0,
                'tenaga_honorer_l' => 0,
                'tenaga_honorer_p' => 0,
                'tenaga_bantuan_l' => 0,
                'tenaga_bantuan_p' => 0,
                'keterangan' => [],
            ];
        };

        $incrementGender = function (array &$row, string $keyPrefix, ?string $gender): void {
            if ($gender === 'L') {
                $row[$keyPrefix.'_l']++;

                return;
            }

            if ($gender === 'P') {
                $row[$keyPrefix.'_p']++;
            }
        };

        foreach ($households as $household) {
            $groupLabel = $this->normalizeDataUmumPkkGroupLabel(
                $this->extractDusunLingkunganName($household)
            );
            $ensureRow($groupLabel);

            $rw = $this->extractRwNumber($household);
            if ($rw !== '-') {
                $grouped[$groupLabel]['pkk_rw'][$rw] = true;
            }

            $rt = $this->extractRtNumber($household);
            if ($rt !== '-') {
                $grouped[$groupLabel]['pkk_rt'][$rt] = true;
            }

            $dasaWisma = $this->normalizeDasaWismaName($household->dasawisma);
            if ($dasaWisma !== '-') {
                $grouped[$groupLabel]['dasa_wisma'][$dasaWisma] = true;
            }

            $grouped[$groupLabel]['krt']++;
            $grouped[$groupLabel]['kk']++;

            $anggota = $household->relationLoaded('anggota') ? $household->anggota : collect();
            $jiwaL = 0;
            $jiwaP = 0;

            if ($anggota->isNotEmpty()) {
                $jiwaL = (int) $anggota->where('jenis_kelamin', 'L')->count();
                $jiwaP = (int) $anggota->where('jenis_kelamin', 'P')->count();
            }

            if ($jiwaL === 0 && $jiwaP === 0) {
                $jiwaL = (int) $household->jumlah_warga_laki_laki;
                $jiwaP = (int) $household->jumlah_warga_perempuan;
            }

            $grouped[$groupLabel]['jiwa_l'] += $jiwaL;
            $grouped[$groupLabel]['jiwa_p'] += $jiwaP;

            $keterangan = trim((string) $household->keterangan);
            if ($keterangan !== '') {
                $grouped[$groupLabel]['keterangan'][] = $keterangan;
            }
        }

        $anggotaTpPkk = AnggotaTimPenggerak::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['jenis_kelamin', 'alamat', 'jabatan', 'keterangan']);

        foreach ($anggotaTpPkk as $item) {
            $groupLabel = $this->normalizeDataUmumPkkGroupLabel(
                $this->extractDusunLingkunganFromText($item->alamat)
            );
            $ensureRow($groupLabel);

            $incrementGender($grouped[$groupLabel], 'anggota_tp_pkk', $item->jenis_kelamin);

            $jabatan = Str::lower(trim((string) $item->jabatan));
            if ($jabatan !== '') {
                if (str_contains($jabatan, 'honorer')) {
                    $incrementGender($grouped[$groupLabel], 'tenaga_honorer', $item->jenis_kelamin);
                }

                if (str_contains($jabatan, 'bantuan')) {
                    $incrementGender($grouped[$groupLabel], 'tenaga_bantuan', $item->jenis_kelamin);
                }
            }

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $grouped[$groupLabel]['keterangan'][] = $keterangan;
            }
        }

        $kaderUmumItems = AnggotaPokja::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['jenis_kelamin', 'alamat', 'keterangan']);

        foreach ($kaderUmumItems as $item) {
            $groupLabel = $this->normalizeDataUmumPkkGroupLabel(
                $this->extractDusunLingkunganFromText($item->alamat)
            );
            $ensureRow($groupLabel);

            $incrementGender($grouped[$groupLabel], 'kader_umum', $item->jenis_kelamin);

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $grouped[$groupLabel]['keterangan'][] = $keterangan;
            }
        }

        $kaderKhususItems = KaderKhusus::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['jenis_kelamin', 'alamat', 'keterangan']);

        foreach ($kaderKhususItems as $item) {
            $groupLabel = $this->normalizeDataUmumPkkGroupLabel(
                $this->extractDusunLingkunganFromText($item->alamat)
            );
            $ensureRow($groupLabel);

            $incrementGender($grouped[$groupLabel], 'kader_khusus', $item->jenis_kelamin);

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $grouped[$groupLabel]['keterangan'][] = $keterangan;
            }
        }

        $sortedGroupLabels = collect(array_keys($grouped))
            ->sort(fn (string $left, string $right): int => $this->compareDataUmumPkkLabels($left, $right))
            ->values();

        $result = collect();

        foreach ($sortedGroupLabels as $groupLabel) {
            $row = $grouped[$groupLabel];

            $result->push([
                'nomor_urut' => $result->count() + 1,
                'nama_dusun_lingkungan_atau_sebutan_lain' => $groupLabel,
                'jumlah_pkk_rw' => count($row['pkk_rw']),
                'jumlah_pkk_rt' => count($row['pkk_rt']),
                'jumlah_dasa_wisma' => count($row['dasa_wisma']),
                'jumlah_krt' => (int) $row['krt'],
                'jumlah_kk' => (int) $row['kk'],
                'jumlah_jiwa_l' => (int) $row['jiwa_l'],
                'jumlah_jiwa_p' => (int) $row['jiwa_p'],
                'jumlah_kader_anggota_tp_pkk_l' => (int) $row['anggota_tp_pkk_l'],
                'jumlah_kader_anggota_tp_pkk_p' => (int) $row['anggota_tp_pkk_p'],
                'jumlah_kader_umum_l' => (int) $row['kader_umum_l'],
                'jumlah_kader_umum_p' => (int) $row['kader_umum_p'],
                'jumlah_kader_khusus_l' => (int) $row['kader_khusus_l'],
                'jumlah_kader_khusus_p' => (int) $row['kader_khusus_p'],
                'jumlah_tenaga_sekretariat_honorer_l' => (int) $row['tenaga_honorer_l'],
                'jumlah_tenaga_sekretariat_honorer_p' => (int) $row['tenaga_honorer_p'],
                'jumlah_tenaga_sekretariat_bantuan_l' => (int) $row['tenaga_bantuan_l'],
                'jumlah_tenaga_sekretariat_bantuan_p' => (int) $row['tenaga_bantuan_p'],
                'keterangan' => $this->composeScalarFieldLabel(collect($row['keterangan'])),
            ]);
        }

        return $result;
    }

    public function getDataUmumPkkKecamatanByLevelAndArea(string $level, int $areaId): Collection
    {
        $scopeArea = Area::query()
            ->with('parent')
            ->find($areaId);

        $fallbackDesaLabel = '-';
        if ($level === 'desa' && $scopeArea?->level === 'desa' && trim((string) $scopeArea->name) !== '') {
            $fallbackDesaLabel = sprintf('DESA %s', trim((string) $scopeArea->name));
        }

        $households = DataWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->with(['anggota', 'area.parent'])
            ->orderBy('id')
            ->get();

        $grouped = [];

        $ensureRow = function (string $label) use (&$grouped): void {
            if (isset($grouped[$label])) {
                return;
            }

            $grouped[$label] = [
                'dusun_lingkungan' => [],
                'pkk_rw' => [],
                'pkk_rt' => [],
                'dasa_wisma' => [],
                'krt' => 0,
                'kk' => 0,
                'jiwa_l' => 0,
                'jiwa_p' => 0,
                'anggota_tp_pkk_l' => 0,
                'anggota_tp_pkk_p' => 0,
                'kader_umum_l' => 0,
                'kader_umum_p' => 0,
                'kader_khusus_l' => 0,
                'kader_khusus_p' => 0,
                'tenaga_honorer_l' => 0,
                'tenaga_honorer_p' => 0,
                'tenaga_bantuan_l' => 0,
                'tenaga_bantuan_p' => 0,
                'keterangan' => [],
            ];
        };

        $incrementGender = function (array &$row, string $keyPrefix, ?string $gender): void {
            if ($gender === 'L') {
                $row[$keyPrefix.'_l']++;

                return;
            }

            if ($gender === 'P') {
                $row[$keyPrefix.'_p']++;
            }
        };

        $appendTopologyFromAddress = function (array &$row, ?string $address): void {
            $dusunLingkungan = $this->extractDusunLingkunganFromText($address);
            if ($dusunLingkungan !== '-') {
                $row['dusun_lingkungan'][$dusunLingkungan] = true;
            }

            $rtNumber = $this->extractRtNumberFromText($address);
            if ($rtNumber !== '-') {
                $row['pkk_rt'][$rtNumber] = true;
            }

            $rwNumber = $this->extractRwNumberFromText($address);
            if ($rwNumber !== '-') {
                $row['pkk_rw'][$rwNumber] = true;
            }
        };

        foreach ($households as $household) {
            $desaLabel = $this->normalizeDataUmumPkkDesaLabel(
                $this->extractDesaKelurahanNameOrFallback($household),
                $fallbackDesaLabel
            );
            $ensureRow($desaLabel);

            $dusunLingkungan = $this->extractDusunLingkunganName($household);
            if ($dusunLingkungan !== '-') {
                $grouped[$desaLabel]['dusun_lingkungan'][$dusunLingkungan] = true;
            }

            $rw = $this->extractRwNumber($household);
            if ($rw !== '-') {
                $grouped[$desaLabel]['pkk_rw'][$rw] = true;
            }

            $rt = $this->extractRtNumber($household);
            if ($rt !== '-') {
                $grouped[$desaLabel]['pkk_rt'][$rt] = true;
            }

            $dasaWisma = $this->normalizeDasaWismaName($household->dasawisma);
            if ($dasaWisma !== '-') {
                $grouped[$desaLabel]['dasa_wisma'][$dasaWisma] = true;
            }

            $grouped[$desaLabel]['krt']++;
            $grouped[$desaLabel]['kk']++;

            $anggota = $household->relationLoaded('anggota') ? $household->anggota : collect();
            $jiwaL = 0;
            $jiwaP = 0;

            if ($anggota->isNotEmpty()) {
                $jiwaL = (int) $anggota->where('jenis_kelamin', 'L')->count();
                $jiwaP = (int) $anggota->where('jenis_kelamin', 'P')->count();
            }

            if ($jiwaL === 0 && $jiwaP === 0) {
                $jiwaL = (int) $household->jumlah_warga_laki_laki;
                $jiwaP = (int) $household->jumlah_warga_perempuan;
            }

            $grouped[$desaLabel]['jiwa_l'] += $jiwaL;
            $grouped[$desaLabel]['jiwa_p'] += $jiwaP;

            $keterangan = trim((string) $household->keterangan);
            if ($keterangan !== '') {
                $grouped[$desaLabel]['keterangan'][] = $keterangan;
            }
        }

        $anggotaTpPkk = AnggotaTimPenggerak::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['jenis_kelamin', 'alamat', 'jabatan', 'keterangan']);

        foreach ($anggotaTpPkk as $item) {
            $desaLabel = $this->normalizeDataUmumPkkDesaLabel(
                $this->extractDesaKelurahanFromText($item->alamat),
                $fallbackDesaLabel
            );
            $ensureRow($desaLabel);

            $appendTopologyFromAddress($grouped[$desaLabel], $item->alamat);
            $incrementGender($grouped[$desaLabel], 'anggota_tp_pkk', $item->jenis_kelamin);

            $jabatan = Str::lower(trim((string) $item->jabatan));
            if ($jabatan !== '') {
                if (str_contains($jabatan, 'honorer')) {
                    $incrementGender($grouped[$desaLabel], 'tenaga_honorer', $item->jenis_kelamin);
                }

                if (str_contains($jabatan, 'bantuan')) {
                    $incrementGender($grouped[$desaLabel], 'tenaga_bantuan', $item->jenis_kelamin);
                }
            }

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $grouped[$desaLabel]['keterangan'][] = $keterangan;
            }
        }

        $kaderUmumItems = AnggotaPokja::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['jenis_kelamin', 'alamat', 'keterangan']);

        foreach ($kaderUmumItems as $item) {
            $desaLabel = $this->normalizeDataUmumPkkDesaLabel(
                $this->extractDesaKelurahanFromText($item->alamat),
                $fallbackDesaLabel
            );
            $ensureRow($desaLabel);

            $appendTopologyFromAddress($grouped[$desaLabel], $item->alamat);
            $incrementGender($grouped[$desaLabel], 'kader_umum', $item->jenis_kelamin);

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $grouped[$desaLabel]['keterangan'][] = $keterangan;
            }
        }

        $kaderKhususItems = KaderKhusus::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['jenis_kelamin', 'alamat', 'keterangan']);

        foreach ($kaderKhususItems as $item) {
            $desaLabel = $this->normalizeDataUmumPkkDesaLabel(
                $this->extractDesaKelurahanFromText($item->alamat),
                $fallbackDesaLabel
            );
            $ensureRow($desaLabel);

            $appendTopologyFromAddress($grouped[$desaLabel], $item->alamat);
            $incrementGender($grouped[$desaLabel], 'kader_khusus', $item->jenis_kelamin);

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $grouped[$desaLabel]['keterangan'][] = $keterangan;
            }
        }

        $sortedGroupLabels = collect(array_keys($grouped))
            ->sort(fn (string $left, string $right): int => $this->compareDataUmumPkkDesaLabels($left, $right))
            ->values();

        $result = collect();

        foreach ($sortedGroupLabels as $groupLabel) {
            $row = $grouped[$groupLabel];

            $result->push([
                'nomor_urut' => $result->count() + 1,
                'nama_desa_kelurahan' => $groupLabel,
                'jumlah_dusun_lingkungan' => count($row['dusun_lingkungan']),
                'jumlah_pkk_rw' => count($row['pkk_rw']),
                'jumlah_pkk_rt' => count($row['pkk_rt']),
                'jumlah_dasa_wisma' => count($row['dasa_wisma']),
                'jumlah_krt' => (int) $row['krt'],
                'jumlah_kk' => (int) $row['kk'],
                'jumlah_jiwa_l' => (int) $row['jiwa_l'],
                'jumlah_jiwa_p' => (int) $row['jiwa_p'],
                'jumlah_kader_anggota_tp_pkk_l' => (int) $row['anggota_tp_pkk_l'],
                'jumlah_kader_anggota_tp_pkk_p' => (int) $row['anggota_tp_pkk_p'],
                'jumlah_kader_umum_l' => (int) $row['kader_umum_l'],
                'jumlah_kader_umum_p' => (int) $row['kader_umum_p'],
                'jumlah_kader_khusus_l' => (int) $row['kader_khusus_l'],
                'jumlah_kader_khusus_p' => (int) $row['kader_khusus_p'],
                'jumlah_tenaga_sekretariat_honorer_l' => (int) $row['tenaga_honorer_l'],
                'jumlah_tenaga_sekretariat_honorer_p' => (int) $row['tenaga_honorer_p'],
                'jumlah_tenaga_sekretariat_bantuan_l' => (int) $row['tenaga_bantuan_l'],
                'jumlah_tenaga_sekretariat_bantuan_p' => (int) $row['tenaga_bantuan_p'],
                'keterangan' => $this->composeScalarFieldLabel(collect($row['keterangan'])),
            ]);
        }

        return $result;
    }

    public function getDataKegiatanPkkPokjaIiiByLevelAndArea(string $level, int $areaId): Collection
    {
        $scopeArea = Area::query()->find($areaId);
        $wilayahLabel = trim((string) ($scopeArea?->name ?? '')) !== ''
            ? trim((string) $scopeArea?->name)
            : 'SEBUTAN LAIN';

        $households = $this->scopedHouseholds($level, $areaId);
        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $householdMetrics = $this->sumHouseholdMetrics($households, $activityFlags);

        $keteranganNotes = [];
        $jumlahKaderPangan = 0;
        $jumlahKaderSandang = 0;
        $jumlahKaderTataLaksanaRumahTangga = 0;

        $anggotaPokjaItems = AnggotaPokja::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['pokja', 'jabatan', 'keterangan']);

        foreach ($anggotaPokjaItems as $item) {
            $pokja = Str::lower(trim((string) $item->pokja));
            if ($pokja !== '' && ! str_contains($pokja, 'iii') && $pokja !== '3') {
                continue;
            }

            $jabatan = Str::lower(trim((string) $item->jabatan));

            if (str_contains($jabatan, 'pangan')) {
                $jumlahKaderPangan++;
            } elseif (str_contains($jabatan, 'sandang')) {
                $jumlahKaderSandang++;
            } else {
                $jumlahKaderTataLaksanaRumahTangga++;
            }

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $keteranganNotes[] = $keterangan;
            }
        }

        $jumlahPeternakan = 0;
        $jumlahPerikanan = 0;
        $jumlahWarungHidup = 0;
        $jumlahLumbungHidup = 0;
        $jumlahToga = 0;
        $jumlahTanamanKeras = 0;
        $jumlahTanamanLainnya = 0;

        $pemanfaatanItems = DataPemanfaatanTanahPekaranganHatinyaPkk::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['kategori_pemanfaatan_lahan', 'komoditi', 'jumlah_komoditi']);

        foreach ($pemanfaatanItems as $item) {
            $jumlahKomoditi = max((int) $item->jumlah_komoditi, 0);
            $kategori = Str::lower(trim((string) $item->kategori_pemanfaatan_lahan));
            $komoditi = Str::lower(trim((string) $item->komoditi));

            if (str_contains($komoditi, 'lumbung')) {
                $jumlahLumbungHidup += $jumlahKomoditi;

                continue;
            }

            if (str_contains($kategori, 'peternakan')) {
                $jumlahPeternakan += $jumlahKomoditi;

                continue;
            }

            if (str_contains($kategori, 'perikanan')) {
                $jumlahPerikanan += $jumlahKomoditi;

                continue;
            }

            if (str_contains($kategori, 'warung')) {
                $jumlahWarungHidup += $jumlahKomoditi;

                continue;
            }

            if (str_contains($kategori, 'toga')) {
                $jumlahToga += $jumlahKomoditi;

                continue;
            }

            if (str_contains($kategori, 'tanaman keras')) {
                $jumlahTanamanKeras += $jumlahKomoditi;

                continue;
            }

            $jumlahTanamanLainnya += $jumlahKomoditi;
        }

        $jumlahIndustriPangan = 0;
        $jumlahIndustriSandang = 0;
        $jumlahIndustriJasa = 0;

        $industriItems = DataIndustriRumahTangga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['kategori_jenis_industri', 'jumlah_komoditi']);

        foreach ($industriItems as $item) {
            $jumlahKomoditi = max((int) $item->jumlah_komoditi, 0);
            $kategori = Str::lower(trim((string) $item->kategori_jenis_industri));

            if (str_contains($kategori, 'pangan')) {
                $jumlahIndustriPangan += $jumlahKomoditi;

                continue;
            }

            if (str_contains($kategori, 'sandang') || str_contains($kategori, 'konveksi')) {
                $jumlahIndustriSandang += $jumlahKomoditi;

                continue;
            }

            if (str_contains($kategori, 'jasa')) {
                $jumlahIndustriJasa += $jumlahKomoditi;
            }
        }

        return collect([
            [
                'nomor_urut' => 1,
                'nama_wilayah' => $wilayahLabel,
                'jumlah_kader_pangan' => $jumlahKaderPangan,
                'jumlah_kader_sandang' => $jumlahKaderSandang,
                'jumlah_kader_tata_laksana_rumah_tangga' => $jumlahKaderTataLaksanaRumahTangga,
                'jumlah_keluarga_beras' => (int) ($householdMetrics['beras'] ?? 0),
                'jumlah_keluarga_non_beras' => (int) ($householdMetrics['non_beras'] ?? 0),
                'jumlah_peternakan' => $jumlahPeternakan,
                'jumlah_perikanan' => $jumlahPerikanan,
                'jumlah_warung_hidup' => $jumlahWarungHidup,
                'jumlah_lumbung_hidup' => $jumlahLumbungHidup,
                'jumlah_toga' => $jumlahToga,
                'jumlah_tanaman_keras' => $jumlahTanamanKeras,
                'jumlah_tanaman_lainnya' => $jumlahTanamanLainnya,
                'jumlah_industri_pangan' => $jumlahIndustriPangan,
                'jumlah_industri_sandang' => $jumlahIndustriSandang,
                'jumlah_industri_jasa' => $jumlahIndustriJasa,
                'jumlah_rumah_sehat_layak_huni' => (int) ($householdMetrics['sehat_layak_huni'] ?? 0),
                'jumlah_rumah_tidak_sehat_tidak_layak_huni' => (int) ($householdMetrics['tidak_sehat_layak_huni'] ?? 0),
                'keterangan' => $this->composeScalarFieldLabel(collect($keteranganNotes)),
            ],
        ]);
    }

    public function getDataKegiatanPkkPokjaIvByLevelAndArea(string $level, int $areaId): Collection
    {
        $scopeArea = Area::query()->find($areaId);
        $wilayahLabel = trim((string) ($scopeArea?->name ?? '')) !== ''
            ? trim((string) $scopeArea?->name)
            : 'SEBUTAN LAIN';

        $households = $this->scopedHouseholds($level, $areaId);
        $members = $households->flatMap(
            fn (DataWarga $household): Collection => $household->relationLoaded('anggota')
                ? $household->anggota
                : collect()
        );

        $activityFlags = $this->buildActivityFlags($level, $areaId);
        $householdMetrics = $this->sumHouseholdMetrics($households, $activityFlags);

        $keteranganNotes = [];
        $jumlahKaderGizi = 0;
        $jumlahKaderKesling = 0;
        $jumlahKaderPhbs = 0;
        $jumlahKaderKb = 0;

        $kaderKhususItems = KaderKhusus::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['jenis_kader_khusus', 'keterangan']);

        foreach ($kaderKhususItems as $item) {
            $jenisKader = Str::lower(trim((string) $item->jenis_kader_khusus));

            if (str_contains($jenisKader, 'gizi')) {
                $jumlahKaderGizi++;
            } elseif (str_contains($jenisKader, 'kesling') || str_contains($jenisKader, 'lingkungan')) {
                $jumlahKaderKesling++;
            } elseif (str_contains($jenisKader, 'phbs')) {
                $jumlahKaderPhbs++;
            } elseif (str_contains($jenisKader, 'kb') || str_contains($jenisKader, 'keluarga berencana')) {
                $jumlahKaderKb++;
            }

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $keteranganNotes[] = $keterangan;
            }
        }

        $posyanduItems = Posyandu::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['jumlah_pengunjung_l', 'jumlah_pengunjung_p']);

        $jumlahPosyandu = $posyanduItems->count();
        $jumlahImunisasiVaksinasiBayiBalita = (int) $posyanduItems
            ->sum(fn (Posyandu $item): int => (int) $item->jumlah_pengunjung_l + (int) $item->jumlah_pengunjung_p);

        $aktivitasKegiatan = DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('aktivitas', true)
            ->get(['kegiatan', 'keterangan']);

        $jumlahPkg = $aktivitasKegiatan
            ->filter(function (DataKegiatanWarga $item): bool {
                $text = Str::lower(trim(sprintf('%s %s', (string) $item->kegiatan, (string) $item->keterangan)));

                return str_contains($text, 'pkg')
                    || str_contains($text, 'pemeriksaan kesehatan gratis');
            })
            ->count();

        $jumlahTbc = $aktivitasKegiatan
            ->filter(function (DataKegiatanWarga $item): bool {
                $text = Str::lower(trim(sprintf('%s %s', (string) $item->kegiatan, (string) $item->keterangan)));

                return str_contains($text, 'tbc')
                    || str_contains($text, 'tuberkulosis');
            })
            ->count();

        foreach ($aktivitasKegiatan as $item) {
            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $keteranganNotes[] = $keterangan;
            }
        }

        $jumlahAkseptorKbL = (int) $members
            ->where('jenis_kelamin', 'L')
            ->filter(fn (DataWargaAnggota $item): bool => (bool) $item->akseptor_kb)
            ->count();
        $jumlahAkseptorKbP = (int) $members
            ->where('jenis_kelamin', 'P')
            ->filter(fn (DataWargaAnggota $item): bool => (bool) $item->akseptor_kb)
            ->count();

        $jumlahKkMemilikiTabunganKeluarga = (int) $households
            ->filter(function (DataWarga $household): bool {
                $anggota = $household->relationLoaded('anggota') ? $household->anggota : collect();

                return $anggota->contains(fn (DataWargaAnggota $item): bool => (bool) $item->memiliki_tabungan);
            })
            ->count();

        $jumlahKkMemilikiAsuransiKesehatan = (int) $households
            ->filter(function (DataWarga $household): bool {
                $anggota = $household->relationLoaded('anggota') ? $household->anggota : collect();

                return $anggota->contains(function (DataWargaAnggota $item): bool {
                    $text = Str::lower(trim((string) $item->keterangan));

                    return $text !== '' && (str_contains($text, 'asuransi') || str_contains($text, 'bpjs'));
                });
            })
            ->count();

        // Lampiran 4.24 kolom PUS dihitung per keluarga (pasangan), bukan per anggota.
        $jumlahPus = (int) $households
            ->filter(function (DataWarga $household): bool {
                $anggota = $household->relationLoaded('anggota') ? $household->anggota : collect();

                return $anggota->contains(
                    fn (DataWargaAnggota $item): bool => $this->isPusCandidate($item)
                );
            })
            ->count();

        $programPrioritasItems = ProgramPrioritas::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->get(['program', 'prioritas_program', 'kegiatan', 'keterangan']);

        $programUnggulanKesehatan = 0;
        $programUnggulanKelestarianLingkunganHidup = 0;
        $programUnggulanPerencanaanSehat = 0;

        foreach ($programPrioritasItems as $item) {
            $text = Str::lower(trim(sprintf(
                '%s %s %s',
                (string) $item->program,
                (string) $item->prioritas_program,
                (string) $item->kegiatan
            )));

            if ($programUnggulanKesehatan === 0 && str_contains($text, 'kesehatan')) {
                $programUnggulanKesehatan = 1;
            }

            if ($programUnggulanKelestarianLingkunganHidup === 0 && str_contains($text, 'lingkungan')) {
                $programUnggulanKelestarianLingkunganHidup = 1;
            }

            if (
                $programUnggulanPerencanaanSehat === 0
                && (str_contains($text, 'perencanaan sehat') || str_contains($text, 'perencanaan'))
            ) {
                $programUnggulanPerencanaanSehat = 1;
            }

            $keterangan = trim((string) $item->keterangan);
            if ($keterangan !== '') {
                $keteranganNotes[] = $keterangan;
            }
        }

        return collect([
            [
                'nomor_urut' => 1,
                'nama_wilayah' => $wilayahLabel,
                'jumlah_kader_kesehatan' => $kaderKhususItems->count(),
                'jumlah_kader_gizi' => $jumlahKaderGizi,
                'jumlah_kader_kesling' => $jumlahKaderKesling,
                'jumlah_kader_phbs' => $jumlahKaderPhbs,
                'jumlah_kader_kb' => $jumlahKaderKb,
                'jumlah_posyandu' => $jumlahPosyandu,
                'jumlah_imunisasi_vaksinasi_bayi_balita' => $jumlahImunisasiVaksinasiBayiBalita,
                'jumlah_pkg' => $jumlahPkg,
                'jumlah_tbc' => $jumlahTbc,
                'jumlah_rumah_memiliki_jamban' => (int) ($householdMetrics['memiliki_mck_septic'] ?? 0),
                'jumlah_rumah_memiliki_spal' => (int) ($householdMetrics['memiliki_spal'] ?? 0),
                'jumlah_rumah_memiliki_tps' => (int) ($householdMetrics['memiliki_tempat_sampah'] ?? 0),
                'jumlah_mck' => (int) ($householdMetrics['memiliki_mck_septic'] ?? 0),
                'jumlah_krt_menggunakan_pdam' => (int) ($householdMetrics['pdam'] ?? 0),
                'jumlah_krt_menggunakan_sumur' => (int) ($householdMetrics['sumur'] ?? 0),
                'jumlah_krt_menggunakan_lain_lain' => (int) ($householdMetrics['dll'] ?? 0),
                'jumlah_pus' => $jumlahPus,
                'jumlah_wus' => (int) ($householdMetrics['wus'] ?? 0),
                'jumlah_akseptor_kb_l' => $jumlahAkseptorKbL,
                'jumlah_akseptor_kb_p' => $jumlahAkseptorKbP,
                'jumlah_kk_memiliki_tabungan_keluarga' => $jumlahKkMemilikiTabunganKeluarga,
                'jumlah_kk_memiliki_asuransi_kesehatan' => $jumlahKkMemilikiAsuransiKesehatan,
                'program_unggulan_kesehatan' => $programUnggulanKesehatan,
                'program_unggulan_kelestarian_lingkungan_hidup' => $programUnggulanKelestarianLingkunganHidup,
                'program_unggulan_perencanaan_sehat' => $programUnggulanPerencanaanSehat,
                'keterangan' => $this->composeScalarFieldLabel(collect($keteranganNotes)),
            ],
        ]);
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

    private function compareDataUmumPkkLabels(string $left, string $right): int
    {
        if ($left === $right) {
            return 0;
        }

        if ($left === 'SEBUTAN LAIN') {
            return 1;
        }

        if ($right === 'SEBUTAN LAIN') {
            return -1;
        }

        return strnatcasecmp($left, $right);
    }

    private function compareDataUmumPkkDesaLabels(string $left, string $right): int
    {
        if ($left === $right) {
            return 0;
        }

        if ($left === 'SEBUTAN LAIN') {
            return 1;
        }

        if ($right === 'SEBUTAN LAIN') {
            return -1;
        }

        return strnatcasecmp($left, $right);
    }

    private function normalizeDataUmumPkkGroupLabel(string $label): string
    {
        $normalized = trim($label);

        if ($normalized === '' || $normalized === '-') {
            return 'SEBUTAN LAIN';
        }

        return $normalized;
    }

    private function normalizeDataUmumPkkDesaLabel(string $label, string $fallbackLabel = '-'): string
    {
        $normalized = trim($label);

        if ($normalized === '' || $normalized === '-') {
            $normalizedFallback = trim($fallbackLabel);

            if ($normalizedFallback !== '' && $normalizedFallback !== '-') {
                return $normalizedFallback;
            }

            return 'SEBUTAN LAIN';
        }

        return $normalized;
    }

    private function extractDesaKelurahanFromText(?string $source): string
    {
        $normalized = trim((string) $source);

        if ($normalized === '') {
            return '-';
        }

        if (preg_match('/\b(DESA|KELURAHAN|KEL\.?)\s+([^,;]+?)(?=\s+DUSUN\b|\s+LINGKUNGAN\b|\s+RT\b|\s+RW\b|$)/i', $normalized, $matches) === 1) {
            $prefix = strtoupper(str_replace('.', '', trim((string) $matches[1])));
            $name = trim((string) $matches[2]);

            if ($name !== '') {
                return sprintf('%s %s', $prefix, $name);
            }
        }

        return '-';
    }

    private function extractDusunLingkunganFromText(?string $source): string
    {
        $normalized = trim((string) $source);

        if ($normalized === '') {
            return '-';
        }

        if (preg_match('/\b(DUSUN|LINGKUNGAN)\s+([^,;]+?)(?=\s+RT\b|\s+RW\b|$)/i', $normalized, $matches) === 1) {
            $prefix = strtoupper(trim((string) $matches[1]));
            $name = trim((string) $matches[2]);

            if ($name !== '') {
                return sprintf('%s %s', $prefix, $name);
            }
        }

        return '-';
    }

    private function extractRtNumberFromText(?string $source): string
    {
        $normalized = trim((string) $source);

        if ($normalized === '') {
            return '-';
        }

        [$rt,] = $this->extractRtRwPair($normalized);
        if ($rt !== null) {
            return $rt;
        }

        if (preg_match('/\bRT(?:\/RW)?\s*[:.\-]?\s*0*(\d{1,3})\b/i', $normalized, $matches) === 1) {
            return str_pad((string) ((int) $matches[1]), 2, '0', STR_PAD_LEFT);
        }

        return '-';
    }

    private function extractRwNumberFromText(?string $source): string
    {
        $normalized = trim((string) $source);

        if ($normalized === '') {
            return '-';
        }

        [, $rw] = $this->extractRtRwPair($normalized);
        if ($rw !== null) {
            return $rw;
        }

        if (preg_match('/\bRW\s*[:.\-]?\s*0*(\d{1,3})\b/i', $normalized, $matches) === 1) {
            return str_pad((string) ((int) $matches[1]), 2, '0', STR_PAD_LEFT);
        }

        return '-';
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

    /**
     * @param Collection<int, mixed> $values
     */
    private function composeScalarFieldLabel(Collection $values): string
    {
        $normalized = $values
            ->map(fn ($value): string => trim((string) $value))
            ->filter(fn (string $value): bool => $value !== '' && $value !== '-')
            ->unique()
            ->values();

        if ($normalized->isEmpty()) {
            return '-';
        }

        return $normalized->implode('; ');
    }
}
