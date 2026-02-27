<?php

namespace App\Domains\Wilayah\Arsip\UseCases;

use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;

class ResolveArsipDocumentDownloadUseCase
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository
    ) {
    }

    /**
     * @return array{name: string, path: string}|null
     */
    public function execute(string $documentName): ?array
    {
        $document = $this->arsipDocumentRepository->findDocumentByName($documentName);
        if (! is_array($document)) {
            return null;
        }

        return [
            'name' => (string) $document['name'],
            'path' => (string) $document['path'],
        ];
    }
}
