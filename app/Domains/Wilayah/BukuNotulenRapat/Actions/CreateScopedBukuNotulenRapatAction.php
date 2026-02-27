<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Actions;

use App\Domains\Wilayah\BukuNotulenRapat\DTOs\BukuNotulenRapatData;
use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Repositories\BukuNotulenRapatRepositoryInterface;
use App\Domains\Wilayah\BukuNotulenRapat\Services\BukuNotulenRapatScopeService;

class CreateScopedBukuNotulenRapatAction
{
    public function __construct(
        private readonly BukuNotulenRapatRepositoryInterface $bukuNotulenRapatRepository,
        private readonly BukuNotulenRapatScopeService $bukuNotulenRapatScopeService
    ) {
    }

    public function execute(array $payload, string $level): BukuNotulenRapat
    {
        $data = BukuNotulenRapatData::fromArray([
            'entry_date' => $payload['entry_date'],
            'title' => $payload['title'],
            'person_name' => $payload['person_name'] ?? null,
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            'level' => $level,
            'area_id' => $this->bukuNotulenRapatScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->bukuNotulenRapatRepository->store($data);
    }
}
