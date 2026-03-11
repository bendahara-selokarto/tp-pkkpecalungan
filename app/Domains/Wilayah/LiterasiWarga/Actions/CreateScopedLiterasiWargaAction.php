<?php

namespace App\Domains\Wilayah\LiterasiWarga\Actions;

use App\Domains\Wilayah\LiterasiWarga\DTOs\LiterasiWargaData;
use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Domains\Wilayah\LiterasiWarga\Repositories\LiterasiWargaRepositoryInterface;
use App\Domains\Wilayah\LiterasiWarga\Services\LiterasiWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedLiterasiWargaAction
{
    public function __construct(
        private readonly LiterasiWargaRepositoryInterface $literasiWargaRepository,
        private readonly LiterasiWargaScopeService $literasiWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): LiterasiWarga
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = LiterasiWargaData::fromArray([
            'jumlah_tiga_buta' => $payload['jumlah_tiga_buta'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->literasiWargaScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->literasiWargaRepository->store($data);
    }
}
