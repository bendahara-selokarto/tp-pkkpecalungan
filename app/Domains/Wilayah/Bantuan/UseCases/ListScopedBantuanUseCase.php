<?php

namespace App\Domains\Wilayah\Bantuan\UseCases;

use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepository;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;

class ListScopedBantuanUseCase
{
    public function __construct(
        private readonly BantuanRepository $bantuanRepository,
        private readonly BantuanScopeService $bantuanScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->bantuanScopeService->requireUserAreaId();

        return $this->bantuanRepository->getByLevelAndArea($level, $areaId);
    }
}
