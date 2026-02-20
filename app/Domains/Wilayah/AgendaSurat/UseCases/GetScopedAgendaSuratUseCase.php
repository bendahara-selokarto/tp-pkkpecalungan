<?php

namespace App\Domains\Wilayah\AgendaSurat\UseCases;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratScopeService;

class GetScopedAgendaSuratUseCase
{
    public function __construct(
        private readonly AgendaSuratRepositoryInterface $agendaSuratRepository,
        private readonly AgendaSuratScopeService $agendaSuratScopeService
    ) {
    }

    public function execute(int $id, string $level): AgendaSurat
    {
        $agendaSurat = $this->agendaSuratRepository->find($id);
        $areaId = $this->agendaSuratScopeService->requireUserAreaId();

        return $this->agendaSuratScopeService->authorizeSameLevelAndArea($agendaSurat, $level, $areaId);
    }
}
