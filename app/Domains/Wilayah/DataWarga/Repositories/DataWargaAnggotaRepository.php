<?php

namespace App\Domains\Wilayah\DataWarga\Repositories;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWargaAnggota;

class DataWargaAnggotaRepository implements DataWargaAnggotaRepositoryInterface
{
    public function syncForDataWarga(DataWarga $dataWarga, array $anggotaRows, string $level, int $areaId, int $createdBy): void
    {
        DataWargaAnggota::query()
            ->where('data_warga_id', $dataWarga->id)
            ->delete();

        if ($anggotaRows === []) {
            return;
        }

        $rows = [];

        foreach (array_values($anggotaRows) as $index => $row) {
            $rows[] = [
                'data_warga_id' => $dataWarga->id,
                'nomor_urut' => (int) ($row['nomor_urut'] ?? ($index + 1)),
                'nomor_registrasi' => $this->normalizeString($row['nomor_registrasi'] ?? null),
                'nomor_ktp_kk' => $this->normalizeString($row['nomor_ktp_kk'] ?? null),
                'nama' => (string) ($row['nama'] ?? ''),
                'jabatan' => $this->normalizeString($row['jabatan'] ?? null),
                'jenis_kelamin' => $this->normalizeGender($row['jenis_kelamin'] ?? null),
                'tempat_lahir' => $this->normalizeString($row['tempat_lahir'] ?? null),
                'tanggal_lahir' => $this->normalizeString($row['tanggal_lahir'] ?? null),
                'umur_tahun' => $this->normalizeInteger($row['umur_tahun'] ?? null),
                'status_perkawinan' => $this->normalizeString($row['status_perkawinan'] ?? null),
                'status_dalam_keluarga' => $this->normalizeString($row['status_dalam_keluarga'] ?? null),
                'agama' => $this->normalizeString($row['agama'] ?? null),
                'alamat' => $this->normalizeString($row['alamat'] ?? null),
                'desa_kel_sejenis' => $this->normalizeString($row['desa_kel_sejenis'] ?? null),
                'pendidikan' => $this->normalizeString($row['pendidikan'] ?? null),
                'pekerjaan' => $this->normalizeString($row['pekerjaan'] ?? null),
                'akseptor_kb' => $this->normalizeBoolean($row['akseptor_kb'] ?? false),
                'aktif_posyandu' => $this->normalizeBoolean($row['aktif_posyandu'] ?? false),
                'ikut_bkb' => $this->normalizeBoolean($row['ikut_bkb'] ?? false),
                'memiliki_tabungan' => $this->normalizeBoolean($row['memiliki_tabungan'] ?? false),
                'ikut_kelompok_belajar' => $this->normalizeBoolean($row['ikut_kelompok_belajar'] ?? false),
                'jenis_kelompok_belajar' => $this->normalizeString($row['jenis_kelompok_belajar'] ?? null),
                'ikut_paud' => $this->normalizeBoolean($row['ikut_paud'] ?? false),
                'ikut_koperasi' => $this->normalizeBoolean($row['ikut_koperasi'] ?? false),
                'keterangan' => $this->normalizeString($row['keterangan'] ?? null),
                'level' => $level,
                'area_id' => $areaId,
                'created_by' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DataWargaAnggota::insert($rows);
    }

    private function normalizeString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function normalizeBoolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    private function normalizeGender(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = strtoupper((string) $value);

        return in_array($normalized, ['L', 'P'], true) ? $normalized : null;
    }
}
