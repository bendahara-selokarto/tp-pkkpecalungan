<?php

namespace App\Domains\Wilayah\Bantuan\Actions;

use App\Domains\Wilayah\Bantuan\DTOs\BantuanData;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepositoryInterface;

class UpdateBantuanAction
{
    public function __construct(
        private readonly BantuanRepositoryInterface $bantuanRepository
    ) {
    }

    public function execute(Bantuan $bantuan, array $payload): Bantuan
    {
        $data = BantuanData::fromArray([
            ...$payload,
            'level' => $bantuan->level,
            'area_id' => $bantuan->area_id,
            'created_by' => $bantuan->created_by,
        ]);

        return $this->bantuanRepository->update($bantuan, $data);
    }
}
