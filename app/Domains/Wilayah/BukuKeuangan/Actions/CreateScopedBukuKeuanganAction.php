<?php

namespace App\Domains\Wilayah\BukuKeuangan\Actions;

use App\Domains\Wilayah\BukuKeuangan\DTOs\BukuKeuanganData;
use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\BukuKeuangan\Repositories\BukuKeuanganRepositoryInterface;
use App\Domains\Wilayah\BukuKeuangan\Services\BukuKeuanganScopeService;

class CreateScopedBukuKeuanganAction
{
    public function __construct(
        private readonly BukuKeuanganRepositoryInterface $bukuKeuanganRepository,
        private readonly BukuKeuanganScopeService $bukuKeuanganScopeService
    ) {
    }

    public function execute(array $payload, string $level): BukuKeuangan
    {
        $data = BukuKeuanganData::fromArray([
            'transaction_date' => $payload['transaction_date'],
            'source' => $payload['source'],
            'description' => $payload['description'],
            'reference_number' => $payload['reference_number'] ?? null,
            'entry_type' => $payload['entry_type'],
            'amount' => $payload['amount'],
            'level' => $level,
            'area_id' => $this->bukuKeuanganScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->bukuKeuanganRepository->store($data);
    }
}
