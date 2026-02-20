<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Actions;

use App\Domains\Wilayah\SimulasiPenyuluhan\DTOs\SimulasiPenyuluhanData;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Services\SimulasiPenyuluhanScopeService;

class CreateScopedSimulasiPenyuluhanAction
{
    public function __construct(
        private readonly SimulasiPenyuluhanRepositoryInterface $simulasiPenyuluhanRepository,
        private readonly SimulasiPenyuluhanScopeService $simulasiPenyuluhanScopeService
    ) {
    }

    public function execute(array $payload, string $level): SimulasiPenyuluhan
    {
        $data = SimulasiPenyuluhanData::fromArray([
            'nama_kegiatan' => $payload['nama_kegiatan'],
            'jenis_simulasi_penyuluhan' => $payload['jenis_simulasi_penyuluhan'],
            'jumlah_kelompok' => $payload['jumlah_kelompok'],
            'jumlah_sosialisasi' => $payload['jumlah_sosialisasi'],
            'jumlah_kader_l' => $payload['jumlah_kader_l'],
            'jumlah_kader_p' => $payload['jumlah_kader_p'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $level,
            'area_id' => $this->simulasiPenyuluhanScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->simulasiPenyuluhanRepository->store($data);
    }
}
