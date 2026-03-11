<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\Actions;

use App\Domains\Wilayah\PraKoperasiUp2k\DTOs\PraKoperasiUp2kData;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\PraKoperasiUp2k\Repositories\PraKoperasiUp2kRepositoryInterface;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class UpdatePraKoperasiUp2kAction
{
    public function __construct(
        private readonly PraKoperasiUp2kRepositoryInterface $praKoperasiUp2kRepository,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(PraKoperasiUp2k $praKoperasiUp2k, array $payload): PraKoperasiUp2k
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = PraKoperasiUp2kData::fromArray([
            'tingkat' => $payload['tingkat'],
            'jumlah_kelompok' => $payload['jumlah_kelompok'],
            'jumlah_peserta' => $payload['jumlah_peserta'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $praKoperasiUp2k->level,
            'area_id' => (int) $praKoperasiUp2k->area_id,
            'created_by' => (int) $praKoperasiUp2k->created_by,
        ]);

        return $this->praKoperasiUp2kRepository->update($praKoperasiUp2k, $data);
    }
}
