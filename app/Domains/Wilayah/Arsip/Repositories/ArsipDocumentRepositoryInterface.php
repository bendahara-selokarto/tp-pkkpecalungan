<?php

namespace App\Domains\Wilayah\Arsip\Repositories;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ArsipDocumentRepositoryInterface
{
    /**
     * @return Collection<int, ArsipDocument>
     */
    public function listVisibleForUser(User $user): Collection;

    public function paginateGlobalForManagement(int $perPage = 10): LengthAwarePaginator;

    public function paginateDesaByKecamatan(
        int $kecamatanAreaId,
        int $perPage = 10,
        ?int $desaId = null,
        ?string $keyword = null
    ): LengthAwarePaginator;

    public function store(array $payload): ArsipDocument;

    public function update(ArsipDocument $arsipDocument, array $payload): ArsipDocument;

    public function delete(ArsipDocument $arsipDocument): void;

    public function incrementDownloadCount(ArsipDocument $arsipDocument): void;
}
