<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Actions;

use App\Domains\Wilayah\SimulasiPenyuluhan\DTOs\SimulasiPenyuluhanData;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;

class UpdateSimulasiPenyuluhanAction
{
    public function __construct(
        private readonly SimulasiPenyuluhanRepositoryInterface $simulasiPenyuluhanRepository
    ) {
    }

    public function execute(SimulasiPenyuluhan $simulasiPenyuluhan, array $payload): SimulasiPenyuluhan
    {
        $data = SimulasiPenyuluhanData::fromArray([
            'nama_kegiatan' => $payload['nama_kegiatan'],
            'jenis_simulasi_penyuluhan' => $payload['jenis_simulasi_penyuluhan'],
            'jumlah_kelompok' => $payload['jumlah_kelompok'],
            'jumlah_sosialisasi' => $payload['jumlah_sosialisasi'],
            'jumlah_kader_l' => $payload['jumlah_kader_l'],
            'jumlah_kader_p' => $payload['jumlah_kader_p'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $simulasiPenyuluhan->level,
            'area_id' => $simulasiPenyuluhan->area_id,
            'created_by' => $simulasiPenyuluhan->created_by,
        ]);

        return $this->simulasiPenyuluhanRepository->update($simulasiPenyuluhan, $data);
    }
}
