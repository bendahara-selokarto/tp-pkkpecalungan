<?php

namespace App\Domains\Wilayah\Arsip\UseCases;

use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListManagedArsipDocumentsUseCase
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository
    ) {
    }

    public function execute(int $perPage = 10): LengthAwarePaginator
    {
        return $this->arsipDocumentRepository
            ->paginateGlobalForManagement($perPage)
            ->through(static fn ($document): array => [
                'id' => (int) $document->id,
                'title' => (string) $document->title,
                'description' => $document->description,
                'original_name' => (string) $document->original_name,
                'extension' => strtoupper((string) $document->extension),
                'size_bytes' => (int) $document->size_bytes,
                'is_global' => (bool) $document->is_global,
                'updated_at' => $document->updated_at?->toIso8601String(),
                'download_count' => (int) $document->download_count,
                'area_name' => $document->area?->name,
                'creator_name' => $document->creator?->name,
            ]);
    }
}
