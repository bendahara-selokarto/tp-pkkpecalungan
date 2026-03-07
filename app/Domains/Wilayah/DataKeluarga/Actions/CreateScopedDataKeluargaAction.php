<?php

namespace App\Domains\Wilayah\DataKeluarga\Actions;

use App\Domains\Wilayah\DataKeluarga\DTOs\DataKeluargaData;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Services\DataKeluargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedDataKeluargaAction
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository,
        private readonly DataKeluargaScopeService $dataKeluargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(array $payload, string $level): DataKeluarga
    {
        $data = DataKeluargaData::fromArray([
            'kategori_keluarga' => $payload['kategori_keluarga'],
            'jumlah_keluarga' => $payload['jumlah_keluarga'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $this->activeBudgetYearContextService->requireForAuthenticatedUser(),
            'level' => $level,
            'area_id' => $this->dataKeluargaScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->dataKeluargaRepository->store($data);
    }
}
