<?php

namespace App\Domains\Wilayah\Bantuan\Actions;

use App\Domains\Wilayah\Bantuan\DTOs\BantuanData;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepositoryInterface;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedBantuanAction
{
    public function __construct(
        private readonly BantuanRepositoryInterface $bantuanRepository,
        private readonly BantuanScopeService $bantuanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(array $payload, string $level): Bantuan
    {
        $data = BantuanData::fromArray([
            ...$payload,
            'tahun_anggaran' => $this->activeBudgetYearContextService->requireForAuthenticatedUser(),
            'level' => $level,
            'area_id' => $this->bantuanScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->bantuanRepository->store($data);
    }
}
