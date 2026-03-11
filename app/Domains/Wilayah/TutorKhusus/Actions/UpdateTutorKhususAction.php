<?php

namespace App\Domains\Wilayah\TutorKhusus\Actions;

use App\Domains\Wilayah\TutorKhusus\DTOs\TutorKhususData;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Domains\Wilayah\TutorKhusus\Repositories\TutorKhususRepositoryInterface;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class UpdateTutorKhususAction
{
    public function __construct(
        private readonly TutorKhususRepositoryInterface $tutorKhususRepository,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(TutorKhusus $tutorKhusus, array $payload): TutorKhusus
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = TutorKhususData::fromArray([
            'jenis_tutor' => $payload['jenis_tutor'],
            'jumlah_tutor' => $payload['jumlah_tutor'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $tutorKhusus->level,
            'area_id' => (int) $tutorKhusus->area_id,
            'created_by' => (int) $tutorKhusus->created_by,
        ]);

        return $this->tutorKhususRepository->update($tutorKhusus, $data);
    }
}
