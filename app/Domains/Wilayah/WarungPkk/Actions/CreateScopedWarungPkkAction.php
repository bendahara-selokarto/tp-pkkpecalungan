<?php

namespace App\Domains\Wilayah\WarungPkk\Actions;

use App\Domains\Wilayah\WarungPkk\DTOs\WarungPkkData;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;
use App\Domains\Wilayah\WarungPkk\Services\WarungPkkScopeService;

class CreateScopedWarungPkkAction
{
    public function __construct(
        private readonly WarungPkkRepositoryInterface $warungPkkRepository,
        private readonly WarungPkkScopeService $warungPkkScopeService
    ) {
    }

    public function execute(array $payload, string $level): WarungPkk
    {
        $data = WarungPkkData::fromArray([
            'nama_warung_pkk' => $payload['nama_warung_pkk'],
            'nama_pengelola' => $payload['nama_pengelola'],
            'komoditi' => $payload['komoditi'],
            'kategori' => $payload['kategori'],
            'volume' => $payload['volume'],
            'level' => $level,
            'area_id' => $this->warungPkkScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->warungPkkRepository->store($data);
    }
}
