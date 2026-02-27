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
     *     name: string,
     *     extension: string,
     *     size_bytes: int,
     *     updated_at: string|null
     * }>
     */
    public function execute(): array
    {
        return collect($this->arsipDocumentRepository->listDocuments())
            ->map(static function (array $document): array {
                $lastModifiedAt = $document['last_modified_at'];

                return [
                    'name' => (string) $document['name'],
                    'extension' => strtoupper((string) $document['extension']),
                    'size_bytes' => (int) $document['size_bytes'],
                    'updated_at' => $lastModifiedAt?->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }
}
