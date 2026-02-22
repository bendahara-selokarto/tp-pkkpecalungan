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

    private function scopedHouseholds(string $level, int $areaId): Collection
    {
        return DataWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->with('anggota')
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
}
