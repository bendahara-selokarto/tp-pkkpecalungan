<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\Actions;

use App\Domains\Wilayah\PraKoperasiUp2k\DTOs\PraKoperasiUp2kData;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\PraKoperasiUp2k\Repositories\PraKoperasiUp2kRepositoryInterface;
use App\Domains\Wilayah\PraKoperasiUp2k\Services\PraKoperasiUp2kScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedPraKoperasiUp2kAction
{
    public function __construct(
        private readonly PraKoperasiUp2kRepositoryInterface $praKoperasiUp2kRepository,
        private readonly PraKoperasiUp2kScopeService $praKoperasiUp2kScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): PraKoperasiUp2k
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = PraKoperasiUp2kData::fromArray([
            'tingkat' => $payload['tingkat'],
            'jumlah_kelompok' => $payload['jumlah_kelompok'],
            'jumlah_peserta' => $payload['jumlah_peserta'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->praKoperasiUp2kScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->praKoperasiUp2kRepository->store($data);
    }
}
