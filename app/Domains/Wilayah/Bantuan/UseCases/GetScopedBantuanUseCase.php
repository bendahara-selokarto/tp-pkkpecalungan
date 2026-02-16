<?php

namespace App\Domains\Wilayah\Bantuan\UseCases;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepository;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;

class GetScopedBantuanUseCase
{
    public function __construct(
        private readonly BantuanRepository $bantuanRepository,
        private readonly BantuanScopeService $bantuanScopeService
    ) {
    }

    public function execute(int $id, string $level): Bantuan
    {
        $bantuan = $this->bantuanRepository->find($id);
        $areaId = $this->bantuanScopeService->requireUserAreaId();

        return $this->bantuanScopeService->authorizeSameLevelAndArea($bantuan, $level, $areaId);
    }
}
