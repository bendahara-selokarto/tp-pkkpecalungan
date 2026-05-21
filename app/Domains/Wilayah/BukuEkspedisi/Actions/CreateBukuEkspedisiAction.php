<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Actions;

use App\Domains\Wilayah\BukuEkspedisi\DTOs\BukuEkspedisiData;
use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use App\Domains\Wilayah\BukuEkspedisi\Repositories\BukuEkspedisiRepositoryInterface;
use App\Domains\Wilayah\BukuEkspedisi\Services\BukuEkspedisiScopeService;
use App\Models\User;

class CreateBukuEkspedisiAction
{
    public function __construct(
        private readonly BukuEkspedisiRepositoryInterface $bukuEkspedisiRepository,
        private readonly BukuEkspedisiScopeService $bukuEkspedisiScopeService
    ) {
    }

    public function execute(User $user, string $level, array $payload): BukuEkspedisi
    {
        $this->bukuEkspedisiScopeService->canAccessLevel($user, $level);

        $areaId = $this->bukuEkspedisiScopeService->requireUserAreaId();
        $tahunAnggaran = $this->bukuEkspedisiScopeService->requireActiveBudgetYear();

        $uploadedFile = $payload['file'];
        $storedPath = $uploadedFile->store('buku-ekspedisi', 'public');

        $data = BukuEkspedisiData::fromArray([
            'title' => $payload['title'],
            'file_path' => $storedPath,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'mime_type' => $uploadedFile->getClientMimeType(),
            'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
            'size_bytes' => (int) $uploadedFile->getSize(),
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->bukuEkspedisiRepository->store($data);
    }
}
