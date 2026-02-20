<?php

namespace App\Domains\Wilayah\Bkr\Actions;

use App\Domains\Wilayah\Bkr\DTOs\BkrData;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;

class UpdateBkrAction
{
    public function __construct(
        private readonly BkrRepositoryInterface $bkrRepository
    ) {
    }

    public function execute(Bkr $bkr, array $payload): Bkr
    {
        $data = BkrData::fromArray([
            'desa' => $payload['desa'],
            'nama_bkr' => $payload['nama_bkr'],
            'no_tgl_sk' => $payload['no_tgl_sk'],
            'nama_ketua_kelompok' => $payload['nama_ketua_kelompok'],
            'jumlah_anggota' => $payload['jumlah_anggota'],
            'kegiatan' => $payload['kegiatan'],
            'level' => $bkr->level,
            'area_id' => $bkr->area_id,
            'created_by' => $bkr->created_by,
        ]);

        return $this->bkrRepository->update($bkr, $data);
    }
}

