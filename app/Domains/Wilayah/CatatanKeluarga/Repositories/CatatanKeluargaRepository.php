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
