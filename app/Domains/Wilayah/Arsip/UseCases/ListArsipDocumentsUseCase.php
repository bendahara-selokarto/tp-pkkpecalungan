<?php

namespace App\Domains\Wilayah\Arsip\UseCases;

use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;

class ListArsipDocumentsUseCase
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository
    ) {
    }

    /**
     * @return list<array{
     *     id: int,
     *     title: string,
     *     description: string|null,
     *     original_name: string,
     *     name: string,
     *     extension: string,
     *     size_bytes: int,
     *     updated_at: string|null,
     *     published_at: string|null
     * }>
     */
    public function execute(): array
    {
        return $this->arsipDocumentRepository->listPublished()
            ->map(static function ($document): array {
                return [
                    'id' => (int) $document->id,
                    'title' => (string) $document->title,
                    'description' => $document->description,
                    'original_name' => (string) $document->original_name,
                    'name' => (string) $document->original_name,
                    'extension' => strtoupper((string) $document->extension),
                    'size_bytes' => (int) $document->size_bytes,
                    'updated_at' => $document->updated_at?->toIso8601String(),
                    'published_at' => $document->published_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }
}
