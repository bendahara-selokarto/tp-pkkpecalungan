<?php

namespace App\Domains\Wilayah\LiterasiWarga\Actions;

use App\Domains\Wilayah\LiterasiWarga\DTOs\LiterasiWargaData;
use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Domains\Wilayah\LiterasiWarga\Repositories\LiterasiWargaRepositoryInterface;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class UpdateLiterasiWargaAction
{
    public function __construct(
        private readonly LiterasiWargaRepositoryInterface $literasiWargaRepository,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(LiterasiWarga $literasiWarga, array $payload): LiterasiWarga
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = LiterasiWargaData::fromArray([
            'jumlah_tiga_buta' => $payload['jumlah_tiga_buta'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $literasiWarga->level,
            'area_id' => (int) $literasiWarga->area_id,
            'created_by' => (int) $literasiWarga->created_by,
        ]);

        return $this->literasiWargaRepository->update($literasiWarga, $data);
    }
}
