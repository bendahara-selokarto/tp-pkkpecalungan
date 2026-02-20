<?php

namespace App\Domains\Wilayah\AgendaSurat\UseCases;

use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratScopeService;

class ListScopedAgendaSuratUseCase
{
    public function __construct(
        private readonly AgendaSuratRepositoryInterface $agendaSuratRepository,
        private readonly AgendaSuratScopeService $agendaSuratScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->agendaSuratScopeService->requireUserAreaId();

        return $this->agendaSuratRepository->getByLevelAndArea($level, $areaId);
    }
}
