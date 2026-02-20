<?php

namespace App\Domains\Wilayah\WarungPkk\Actions;

use App\Domains\Wilayah\WarungPkk\DTOs\WarungPkkData;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;

class UpdateWarungPkkAction
{
    public function __construct(
        private readonly WarungPkkRepositoryInterface $warungPkkRepository
    ) {
    }

    public function execute(WarungPkk $warungPkk, array $payload): WarungPkk
    {
        $data = WarungPkkData::fromArray([
            'nama_warung_pkk' => $payload['nama_warung_pkk'],
            'nama_pengelola' => $payload['nama_pengelola'],
            'komoditi' => $payload['komoditi'],
            'kategori' => $payload['kategori'],
            'volume' => $payload['volume'],
            'level' => $warungPkk->level,
            'area_id' => $warungPkk->area_id,
            'created_by' => $warungPkk->created_by,
        ]);

        return $this->warungPkkRepository->update($warungPkk, $data);
    }
}
