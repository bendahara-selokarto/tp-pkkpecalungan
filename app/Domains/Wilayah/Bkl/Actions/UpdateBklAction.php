<?php

namespace App\Domains\Wilayah\Bkl\Actions;

use App\Domains\Wilayah\Bkl\DTOs\BklData;
use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;

class UpdateBklAction
{
    public function __construct(
        private readonly BklRepositoryInterface $bklRepository
    ) {
    }

    public function execute(Bkl $bkl, array $payload): Bkl
    {
        $data = BklData::fromArray([
            'desa' => $payload['desa'],
            'nama_bkl' => $payload['nama_bkl'],
            'no_tgl_sk' => $payload['no_tgl_sk'],
            'nama_ketua_kelompok' => $payload['nama_ketua_kelompok'],
            'jumlah_anggota' => $payload['jumlah_anggota'],
            'kegiatan' => $payload['kegiatan'],
            'level' => $bkl->level,
            'area_id' => $bkl->area_id,
            'created_by' => $bkl->created_by,
        ]);

        return $this->bklRepository->update($bkl, $data);
    }
}
