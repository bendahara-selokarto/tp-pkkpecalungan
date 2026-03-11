<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\Actions;

use App\Domains\Wilayah\PelatihanKaderPokjaIi\DTOs\PelatihanKaderPokjaIiData;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Repositories\PelatihanKaderPokjaIiRepositoryInterface;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Services\PelatihanKaderPokjaIiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedPelatihanKaderPokjaIiAction
{
    public function __construct(
        private readonly PelatihanKaderPokjaIiRepositoryInterface $pelatihanKaderPokjaIiRepository,
        private readonly PelatihanKaderPokjaIiScopeService $pelatihanKaderPokjaIiScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): PelatihanKaderPokjaIi
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = PelatihanKaderPokjaIiData::fromArray([
            'kategori_pelatihan' => $payload['kategori_pelatihan'],
            'jumlah_kader' => $payload['jumlah_kader'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->pelatihanKaderPokjaIiScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->pelatihanKaderPokjaIiRepository->store($data);
    }
}
