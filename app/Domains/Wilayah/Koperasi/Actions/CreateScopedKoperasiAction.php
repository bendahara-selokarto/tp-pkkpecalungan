<?php

namespace App\Domains\Wilayah\Koperasi\Actions;

use App\Domains\Wilayah\Koperasi\DTOs\KoperasiData;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepositoryInterface;
use App\Domains\Wilayah\Koperasi\Services\KoperasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedKoperasiAction
{
    public function __construct(
        private readonly KoperasiRepositoryInterface $koperasiRepository,
        private readonly KoperasiScopeService $koperasiScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): Koperasi
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = KoperasiData::fromArray([
            'nama_koperasi' => $payload['nama_koperasi'],
            'jenis_usaha' => $payload['jenis_usaha'],
            'berbadan_hukum' => $payload['berbadan_hukum'],
            'belum_berbadan_hukum' => $payload['belum_berbadan_hukum'],
            'jumlah_anggota_l' => $payload['jumlah_anggota_l'],
            'jumlah_anggota_p' => $payload['jumlah_anggota_p'],
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->koperasiScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->koperasiRepository->store($data);
    }
}

