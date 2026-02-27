<?php

namespace App\Domains\Wilayah\BukuTamu\Actions;

use App\Domains\Wilayah\BukuTamu\DTOs\BukuTamuData;
use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\BukuTamu\Repositories\BukuTamuRepositoryInterface;
use App\Domains\Wilayah\BukuTamu\Services\BukuTamuScopeService;

class CreateScopedBukuTamuAction
{
    public function __construct(
        private readonly BukuTamuRepositoryInterface $bukuTamuRepository,
        private readonly BukuTamuScopeService $bukuTamuScopeService
    ) {
    }

    public function execute(array $payload, string $level): BukuTamu
    {
        $data = BukuTamuData::fromArray([
            'visit_date' => $payload['visit_date'],
            'guest_name' => $payload['guest_name'],
            'purpose' => $payload['purpose'],
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            'level' => $level,
            'area_id' => $this->bukuTamuScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->bukuTamuRepository->store($data);
    }
}
