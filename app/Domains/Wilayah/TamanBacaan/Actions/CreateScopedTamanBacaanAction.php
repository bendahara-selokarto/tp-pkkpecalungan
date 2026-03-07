<?php

namespace App\Domains\Wilayah\TamanBacaan\Actions;

use App\Domains\Wilayah\TamanBacaan\DTOs\TamanBacaanData;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;
use App\Domains\Wilayah\TamanBacaan\Services\TamanBacaanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedTamanBacaanAction
{
    public function __construct(
        private readonly TamanBacaanRepositoryInterface $tamanBacaanRepository,
        private readonly TamanBacaanScopeService $tamanBacaanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): TamanBacaan
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = TamanBacaanData::fromArray([
            'nama_taman_bacaan' => $payload['nama_taman_bacaan'],
            'nama_pengelola' => $payload['nama_pengelola'],
            'jumlah_buku_bacaan' => $payload['jumlah_buku_bacaan'],
            'jenis_buku' => $payload['jenis_buku'],
            'kategori' => $payload['kategori'],
            'jumlah' => $payload['jumlah'],
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->tamanBacaanScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->tamanBacaanRepository->store($data);
    }
}

