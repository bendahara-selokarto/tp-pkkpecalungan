<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Actions;

use App\Domains\Wilayah\BukuDaftarHadir\DTOs\BukuDaftarHadirData;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\BukuDaftarHadir\Repositories\BukuDaftarHadirRepositoryInterface;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;

class UpdateBukuDaftarHadirAction
{
    public function __construct(
        private readonly BukuDaftarHadirRepositoryInterface $bukuDaftarHadirRepository,
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService
    ) {
    }

    public function execute(BukuDaftarHadir $bukuDaftarHadir, array $payload): BukuDaftarHadir
    {
        $this->bukuDaftarHadirScopeService->authorizeActivityScope(
            (int) $payload['activity_id'],
            $bukuDaftarHadir->level,
            (int) $bukuDaftarHadir->area_id
        );

        $data = BukuDaftarHadirData::fromArray([
            'attendance_date' => $payload['attendance_date'],
            'activity_id' => (int) $payload['activity_id'],
            'attendee_name' => $payload['attendee_name'],
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            'level' => $bukuDaftarHadir->level,
            'area_id' => $bukuDaftarHadir->area_id,
            'created_by' => $bukuDaftarHadir->created_by,
        ]);

        return $this->bukuDaftarHadirRepository->update($bukuDaftarHadir, $data);
    }
}
