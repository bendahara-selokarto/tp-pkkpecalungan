<?php

namespace App\Domains\Wilayah\Paar\Actions;

use App\Domains\Wilayah\Paar\DTOs\PaarData;
use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Paar\Repositories\PaarRepositoryInterface;
use App\Domains\Wilayah\Paar\Services\PaarScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedPaarAction
{
    public function __construct(
        private readonly PaarRepositoryInterface $paarRepository,
        private readonly PaarScopeService $paarScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(array $payload, string $level): Paar
    {
        $data = PaarData::fromArray([
            'indikator' => $payload['indikator'],
            'jumlah' => $payload['jumlah'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $this->activeBudgetYearContextService->requireForAuthenticatedUser(),
            'level' => $level,
            'area_id' => $this->paarScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->paarRepository->store($data);
    }
}
