<?php

namespace App\Domains\Wilayah\DataWarga\Actions;

use App\Domains\Wilayah\DataWarga\DTOs\DataWargaData;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaAnggotaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateDataWargaAction
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly DataWargaAnggotaRepositoryInterface $dataWargaAnggotaRepository
    ) {}

    public function execute(DataWarga $dataWarga, array $payload): DataWarga
    {
        $payload = $this->mergeSummaryFromAnggota($payload);

        $alamatDetail = $payload['alamat_detail'] ?? null;
        $dusun = $payload['dusun'] ?? null;
        $rt = $payload['rt'] ?? null;
        $rw = $payload['rw'] ?? null;
        $alamatFull = $payload['alamat'] ?? '';

        if ($rt !== null || $rw !== null || $dusun !== null) {
            $parts = [];
            if ($alamatDetail !== null && trim((string) $alamatDetail) !== '') {
                $parts[] = trim((string) $alamatDetail);
            }
            if ($dusun !== null && trim((string) $dusun) !== '') {
                $parts[] = 'Dusun ' . trim((string) $dusun);
            }
            if ($rt !== null || $rw !== null) {
                $parts[] = 'RT ' . ($rt ?: '0') . ' / RW ' . ($rw ?: '0');
            }
            $alamatFull = implode(' ', $parts);
        } else {
            $rt = $this->extractRt($alamatFull);
            $rw = $this->extractRw($alamatFull);
            $dusun = $this->extractDusun($alamatFull);
            $alamatDetail = $alamatFull;
        }

        $data = DataWargaData::fromArray([
            'dasawisma' => $payload['dasawisma'],
            'nama_kepala_keluarga' => $payload['nama_kepala_keluarga'],
            'alamat' => $alamatFull,
            'rt' => $rt ?? '',
            'rw' => $rw ?? '',
            'dusun' => $dusun,
            'alamat_detail' => $alamatDetail,
            'jumlah_warga_laki_laki' => $payload['jumlah_warga_laki_laki'],
            'jumlah_warga_perempuan' => $payload['jumlah_warga_perempuan'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $dataWarga->tahun_anggaran,
            'level' => $dataWarga->level,
            'area_id' => $dataWarga->area_id,
            'created_by' => $dataWarga->created_by,
        ]);

        return DB::transaction(function () use ($dataWarga, $data, $payload): DataWarga {
            $updated = $this->dataWargaRepository->update($dataWarga, $data);

            if (array_key_exists('anggota', $payload)) {
                $this->dataWargaAnggotaRepository->syncForDataWarga(
                    $updated,
                    is_array($payload['anggota']) ? $payload['anggota'] : [],
                    $updated->level,
                    $updated->area_id,
                    $updated->created_by,
                    $updated->tahun_anggaran
                );
            }

            return $updated;
        });
    }

    private function mergeSummaryFromAnggota(array $payload): array
    {
        if (! array_key_exists('anggota', $payload) || ! is_array($payload['anggota'])) {
            return $payload;
        }

        $jumlahLakiLaki = 0;
        $jumlahPerempuan = 0;

        foreach ($payload['anggota'] as $row) {
            $gender = strtoupper((string) ($row['jenis_kelamin'] ?? ''));

            if ($gender === 'L') {
                $jumlahLakiLaki++;
            }

            if ($gender === 'P') {
                $jumlahPerempuan++;
            }
        }

        $payload['jumlah_warga_laki_laki'] = $jumlahLakiLaki;
        $payload['jumlah_warga_perempuan'] = $jumlahPerempuan;

        return $payload;
    }

    private function extractRt(string $address): string
    {
        if (preg_match('/\bRT(?:\/RW)?\s*[:.\-]?\s*0*(\d{1,3})\b/i', $address, $matches) === 1) {
            return str_pad((string) ((int) $matches[1]), 2, '0', STR_PAD_LEFT);
        }
        return '-';
    }

    private function extractRw(string $address): string
    {
        if (preg_match('/\bRW\s*[:.\-]?\s*0*(\d{1,3})\b/i', $address, $matches) === 1) {
            return str_pad((string) ((int) $matches[1]), 2, '0', STR_PAD_LEFT);
        }
        return '-';
    }

    private function extractDusun(string $address): ?string
    {
        if (preg_match('/\b(DUSUN|LINGKUNGAN)\s+([^,;]+?)(?=\s+RT\b|\s+RW\b|$)/i', $address, $matches) === 1) {
            return trim($matches[2]);
        }
        return null;
    }
}
