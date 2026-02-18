<?php

namespace App\Domains\Wilayah\Bantuan\UseCases;

use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepositoryInterface;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;

class ListScopedBantuanUseCase
{
    public function __construct(
        private readonly BantuanRepositoryInterface $bantuanRepository,
        private readonly BantuanScopeService $bantuanScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->bantuanScopeService->requireUserAreaId();

        return $this->bantuanRepository->getByLevelAndArea($level, $areaId);
    }
}

