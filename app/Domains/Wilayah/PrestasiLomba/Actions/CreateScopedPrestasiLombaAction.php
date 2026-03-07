<?php

namespace App\Domains\Wilayah\PrestasiLomba\Actions;

use App\Domains\Wilayah\PrestasiLomba\DTOs\PrestasiLombaData;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepositoryInterface;
use App\Domains\Wilayah\PrestasiLomba\Services\PrestasiLombaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedPrestasiLombaAction
{
    public function __construct(
        private readonly PrestasiLombaRepositoryInterface $prestasiLombaRepository,
        private readonly PrestasiLombaScopeService $prestasiLombaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(array $payload, string $level): PrestasiLomba
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        $data = PrestasiLombaData::fromArray([
            'tahun' => $payload['tahun'],
            'jenis_lomba' => $payload['jenis_lomba'],
            'lokasi' => $payload['lokasi'],
            'prestasi_kecamatan' => $payload['prestasi_kecamatan'],
            'prestasi_kabupaten' => $payload['prestasi_kabupaten'],
            'prestasi_provinsi' => $payload['prestasi_provinsi'],
            'prestasi_nasional' => $payload['prestasi_nasional'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->prestasiLombaScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->prestasiLombaRepository->store($data);
    }
}
