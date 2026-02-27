<?php

namespace App\Domains\Wilayah\BukuTamu\Actions;

use App\Domains\Wilayah\BukuTamu\DTOs\BukuTamuData;
use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\BukuTamu\Repositories\BukuTamuRepositoryInterface;

class UpdateBukuTamuAction
{
    public function __construct(
        private readonly BukuTamuRepositoryInterface $bukuTamuRepository
    ) {
    }

    public function execute(BukuTamu $bukuTamu, array $payload): BukuTamu
    {
        $data = BukuTamuData::fromArray([
            'visit_date' => $payload['visit_date'],
            'guest_name' => $payload['guest_name'],
            'purpose' => $payload['purpose'],
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            'level' => $bukuTamu->level,
            'area_id' => $bukuTamu->area_id,
            'created_by' => $bukuTamu->created_by,
        ]);

        return $this->bukuTamuRepository->update($bukuTamu, $data);
    }
}
