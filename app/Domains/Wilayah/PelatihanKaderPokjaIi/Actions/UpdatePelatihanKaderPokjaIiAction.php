<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\Actions;

use App\Domains\Wilayah\PelatihanKaderPokjaIi\DTOs\PelatihanKaderPokjaIiData;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Repositories\PelatihanKaderPokjaIiRepositoryInterface;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class UpdatePelatihanKaderPokjaIiAction
{
    public function __construct(
        private readonly PelatihanKaderPokjaIiRepositoryInterface $pelatihanKaderPokjaIiRepository,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(PelatihanKaderPokjaIi $pelatihanKaderPokjaIi, array $payload): PelatihanKaderPokjaIi
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = PelatihanKaderPokjaIiData::fromArray([
            'kategori_pelatihan' => $payload['kategori_pelatihan'],
            'jumlah_kader' => $payload['jumlah_kader'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $pelatihanKaderPokjaIi->level,
            'area_id' => (int) $pelatihanKaderPokjaIi->area_id,
            'created_by' => (int) $pelatihanKaderPokjaIi->created_by,
        ]);

        return $this->pelatihanKaderPokjaIiRepository->update($pelatihanKaderPokjaIi, $data);
    }
}
