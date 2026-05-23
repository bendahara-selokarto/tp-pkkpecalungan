<?php

namespace App\Domains\Wilayah\FotoKegiatan\Actions;

use App\Domains\Wilayah\FotoKegiatan\DTOs\FotoKegiatanData;
use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use App\Domains\Wilayah\FotoKegiatan\Repositories\FotoKegiatanRepositoryInterface;
use App\Domains\Wilayah\FotoKegiatan\Services\FotoKegiatanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Http\UploadedFile;

class CreateScopedFotoKegiatanAction
{
    public function __construct(
        private readonly FotoKegiatanRepositoryInterface $fotoKegiatanRepository,
        private readonly FotoKegiatanScopeService $fotoKegiatanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): FotoKegiatan
    {
        $user = auth()->user();
        $storedPath = null;
        
        if (isset($payload['image_upload']) && $payload['image_upload'] instanceof UploadedFile) {
            $storedPath = $payload['image_upload']->store('foto-kegiatans', 'public');
        }

        $tahunAnggaran = isset($payload['activity_date']) 
            ? (int) date('Y', strtotime($payload['activity_date']))
            : $this->activeBudgetYearContextService->resolveForUser($user);

        $data = FotoKegiatanData::fromArray([
            'activity_date' => $payload['activity_date'],
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'image_path' => $storedPath,
            'level' => $level,
            'area_id' => $this->fotoKegiatanScopeService->requireUserAreaId(),
            'group' => $payload['group'] ?? $this->resolveGroup($user),
            'created_by' => $user->id,
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->fotoKegiatanRepository->store($data);
    }

    private function resolveGroup($user): string
    {
        $groups = $this->fotoKegiatanScopeService->resolveGroupsForUser($user);
        return $groups[0] ?? 'pokja-ii';
    }
}
