<?php

namespace App\Domains\Wilayah\Paar\Actions;

use App\Domains\Wilayah\Paar\DTOs\PaarData;
use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Paar\Repositories\PaarRepositoryInterface;

class UpdatePaarAction
{
    public function __construct(
        private readonly PaarRepositoryInterface $paarRepository
    ) {
    }

    public function execute(Paar $paar, array $payload): Paar
    {
        $data = PaarData::fromArray([
            'indikator' => $paar->indikator,
            'jumlah' => $payload['jumlah'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $paar->level,
            'area_id' => (int) $paar->area_id,
            'created_by' => (int) $paar->created_by,
        ]);

        return $this->paarRepository->update($paar, $data);
    }
}