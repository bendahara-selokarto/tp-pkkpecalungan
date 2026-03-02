<?php

namespace App\Domains\Wilayah\Arsip\UseCases;

use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListKecamatanDesaArsipDocumentsUseCase
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository
    ) {
    }

    public function execute(
        int $kecamatanAreaId,
        int $perPage,
        ?int $desaId = null,
        ?string $keyword = null
    ): LengthAwarePaginator {
        return $this->arsipDocumentRepository->paginateDesaByKecamatan(
            $kecamatanAreaId,
            $perPage,
            $desaId,
            $keyword
        );
    }
}
