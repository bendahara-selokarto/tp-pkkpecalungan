<?php

namespace App\Domains\Wilayah\BukuAgendaSk\UseCases;

use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\BukuAgendaSk\Repositories\BukuAgendaSkRepositoryInterface;
use App\Domains\Wilayah\BukuAgendaSk\Services\BukuAgendaSkScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBukuAgendaSkUseCase
{
    public function __construct(
        private readonly BukuAgendaSkRepositoryInterface $bukuAgendaSkRepository,
        private readonly BukuAgendaSkScopeService $bukuAgendaSkScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): BukuAgendaSk
    {
        $areaId = $this->bukuAgendaSkScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $item = $this->bukuAgendaSkRepository->find($id);

        if ($item->level !== $level || $item->area_id !== $areaId || $item->tahun_anggaran !== $tahunAnggaran) {
            throw new \RuntimeException('Access denied: item does not belong to your area, level, or active budget year.');
        }

        return $item;
    }
}
