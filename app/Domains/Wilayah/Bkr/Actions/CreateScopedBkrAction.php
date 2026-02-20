<?php

namespace App\Domains\Wilayah\Bkr\Actions;

use App\Domains\Wilayah\Bkr\DTOs\BkrData;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;
use App\Domains\Wilayah\Bkr\Services\BkrScopeService;

class CreateScopedBkrAction
{
    public function __construct(
        private readonly BkrRepositoryInterface $bkrRepository,
        private readonly BkrScopeService $bkrScopeService
    ) {
    }

    public function execute(array $payload, string $level): Bkr
    {
        $data = BkrData::fromArray([
            'desa' => $payload['desa'],
            'nama_bkr' => $payload['nama_bkr'],
            'no_tgl_sk' => $payload['no_tgl_sk'],
            'nama_ketua_kelompok' => $payload['nama_ketua_kelompok'],
            'jumlah_anggota' => $payload['jumlah_anggota'],
            'kegiatan' => $payload['kegiatan'],
            'level' => $level,
            'area_id' => $this->bkrScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->bkrRepository->store($data);
    }
}

