<?php

namespace App\Domains\Wilayah\DataWarga\Actions;

use App\Domains\Wilayah\DataWarga\DTOs\DataWargaData;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaAnggotaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Support\Facades\DB;

class CreateScopedDataWargaAction
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly DataWargaAnggotaRepositoryInterface $dataWargaAnggotaRepository,
        private readonly DataWargaScopeService $dataWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(array $payload, string $level): DataWarga
    {
        $payload = $this->mergeSummaryFromAnggota($payload);
        $areaId = $this->dataWargaScopeService->requireUserAreaId();
        $createdBy = (int) auth()->id();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

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
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $createdBy,
        ]);

        return DB::transaction(function () use ($data, $payload, $level, $areaId, $createdBy, $tahunAnggaran): DataWarga {
            $dataWarga = $this->dataWargaRepository->store($data);

            if (array_key_exists('anggota', $payload)) {
                $this->dataWargaAnggotaRepository->syncForDataWarga(
                    $dataWarga,
                    is_array($payload['anggota']) ? $payload['anggota'] : [],
                    $level,
                    $areaId,
                    $createdBy,
                    $tahunAnggaran
                );
            }

            return $dataWarga;
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
