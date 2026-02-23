<?php

namespace App\Domains\Wilayah\BukuKeuangan\Actions;

use App\Domains\Wilayah\BukuKeuangan\DTOs\BukuKeuanganData;
use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\BukuKeuangan\Repositories\BukuKeuanganRepositoryInterface;

class UpdateBukuKeuanganAction
{
    public function __construct(
        private readonly BukuKeuanganRepositoryInterface $bukuKeuanganRepository
    ) {
    }

    public function execute(BukuKeuangan $bukuKeuangan, array $payload): BukuKeuangan
    {
        $data = BukuKeuanganData::fromArray([
            'transaction_date' => $payload['transaction_date'],
            'source' => $payload['source'],
            'description' => $payload['description'],
            'reference_number' => $payload['reference_number'] ?? null,
            'entry_type' => $payload['entry_type'],
            'amount' => $payload['amount'],
            'level' => $bukuKeuangan->level,
            'area_id' => $bukuKeuangan->area_id,
            'created_by' => $bukuKeuangan->created_by,
        ]);

        return $this->bukuKeuanganRepository->update($bukuKeuangan, $data);
    }
}
