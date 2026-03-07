<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Actions;

use App\Domains\Wilayah\DataKegiatanWarga\DTOs\DataKegiatanWargaData;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepositoryInterface;
use App\Domains\Wilayah\DataKegiatanWarga\Services\DataKegiatanWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedDataKegiatanWargaAction
{
    public function __construct(
        private readonly DataKegiatanWargaRepositoryInterface $dataKegiatanWargaRepository,
        private readonly DataKegiatanWargaScopeService $dataKegiatanWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(array $payload, string $level): DataKegiatanWarga
    {
        $data = DataKegiatanWargaData::fromArray([
            'kegiatan' => $payload['kegiatan'],
            'aktivitas' => $payload['aktivitas'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $this->activeBudgetYearContextService->requireForAuthenticatedUser(),
            'level' => $level,
            'area_id' => $this->dataKegiatanWargaScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->dataKegiatanWargaRepository->store($data);
    }
}
