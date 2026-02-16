<?php

namespace App\Domains\Wilayah\Bantuan\Actions;

use App\Domains\Wilayah\Bantuan\DTOs\BantuanData;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepository;

class UpdateBantuanAction
{
    public function __construct(
        private readonly BantuanRepository $bantuanRepository
    ) {
    }

    public function execute(Bantuan $bantuan, array $payload): Bantuan
    {
        $data = BantuanData::fromArray([
            'name' => $payload['name'],
            'category' => $payload['category'],
            'description' => $payload['description'] ?? null,
            'source' => $payload['source'],
            'amount' => $payload['amount'],
            'received_date' => $payload['received_date'],
            'level' => $bantuan->level,
            'area_id' => $bantuan->area_id,
            'created_by' => $bantuan->created_by,
        ]);

        return $this->bantuanRepository->update($bantuan, $data);
    }
}
