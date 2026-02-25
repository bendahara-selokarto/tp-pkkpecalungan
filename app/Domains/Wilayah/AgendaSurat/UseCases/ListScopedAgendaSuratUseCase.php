<?php

namespace App\Domains\Wilayah\AgendaSurat\UseCases;

use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedAgendaSuratUseCase
{
    public function __construct(
        private readonly AgendaSuratRepositoryInterface $agendaSuratRepository,
        private readonly AgendaSuratScopeService $agendaSuratScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->agendaSuratScopeService->requireUserAreaId();

        return $this->agendaSuratRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->agendaSuratScopeService->requireUserAreaId();

        return $this->agendaSuratRepository->getByLevelAndArea($level, $areaId);
    }
}
