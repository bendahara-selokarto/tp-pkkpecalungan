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
    ) {
    }

    public function execute(DataWarga $dataWarga, array $payload): DataWarga
    {
        $payload = $this->mergeSummaryFromAnggota($payload);

        $data = DataWargaData::fromArray([
            'dasawisma' => $payload['dasawisma'],
            'nama_kepala_keluarga' => $payload['nama_kepala_keluarga'],
            'alamat' => $payload['alamat'],
            'jumlah_warga_laki_laki' => $payload['jumlah_warga_laki_laki'],
            'jumlah_warga_perempuan' => $payload['jumlah_warga_perempuan'],
            'keterangan' => $payload['keterangan'] ?? null,
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
                    $updated->created_by
                );
            }

            return $updated;
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
