<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Actions;

use App\Domains\Wilayah\BukuDaftarHadir\DTOs\BukuDaftarHadirData;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\BukuDaftarHadir\Repositories\BukuDaftarHadirRepositoryInterface;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;

class CreateScopedBukuDaftarHadirAction
{
    public function __construct(
        private readonly BukuDaftarHadirRepositoryInterface $bukuDaftarHadirRepository,
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService
    ) {
    }

    public function execute(array $payload, string $level): BukuDaftarHadir
    {
        $areaId = $this->bukuDaftarHadirScopeService->requireUserAreaId();
        $this->bukuDaftarHadirScopeService->authorizeActivityScope(
            (int) $payload['activity_id'],
            $level,
            $areaId
        );

        $data = BukuDaftarHadirData::fromArray([
            'attendance_date' => $payload['attendance_date'],
            'activity_id' => (int) $payload['activity_id'],
            'attendee_name' => $payload['attendee_name'],
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => auth()->id(),
        ]);

        return $this->bukuDaftarHadirRepository->store($data);
    }
}
