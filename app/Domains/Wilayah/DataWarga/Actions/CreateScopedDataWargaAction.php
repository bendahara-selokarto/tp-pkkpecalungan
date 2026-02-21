<?php

namespace App\Domains\Wilayah\DataWarga\Actions;

use App\Domains\Wilayah\DataWarga\DTOs\DataWargaData;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaAnggotaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;
use Illuminate\Support\Facades\DB;

class CreateScopedDataWargaAction
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly DataWargaAnggotaRepositoryInterface $dataWargaAnggotaRepository,
        private readonly DataWargaScopeService $dataWargaScopeService
    ) {
    }

    public function execute(array $payload, string $level): DataWarga
    {
        $payload = $this->mergeSummaryFromAnggota($payload);
        $areaId = $this->dataWargaScopeService->requireUserAreaId();
        $createdBy = (int) auth()->id();

        $data = DataWargaData::fromArray([
            'dasawisma' => $payload['dasawisma'],
            'nama_kepala_keluarga' => $payload['nama_kepala_keluarga'],
            'alamat' => $payload['alamat'],
            'jumlah_warga_laki_laki' => $payload['jumlah_warga_laki_laki'],
            'jumlah_warga_perempuan' => $payload['jumlah_warga_perempuan'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $createdBy,
        ]);

        return DB::transaction(function () use ($data, $payload, $level, $areaId, $createdBy): DataWarga {
            $dataWarga = $this->dataWargaRepository->store($data);

            if (array_key_exists('anggota', $payload)) {
                $this->dataWargaAnggotaRepository->syncForDataWarga(
                    $dataWarga,
                    is_array($payload['anggota']) ? $payload['anggota'] : [],
                    $level,
                    $areaId,
                    $createdBy
                );
            }

            return $dataWarga;
        });
    }

    private function mergeSummaryFromAnggota(array $payload): array
    {
        if (!array_key_exists('anggota', $payload) || !is_array($payload['anggota'])) {
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
}
