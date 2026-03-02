<?php

namespace App\Domains\Wilayah\Arsip\UseCases;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use App\Models\User;
use App\Policies\ArsipDocumentPolicy;

class ResolveArsipDocumentDownloadUseCase
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository,
        private readonly ArsipDocumentPolicy $arsipDocumentPolicy
    ) {
    }

    /**
     * @return array{download_name: string, storage_path: string}|null
     */
    public function execute(User $user, ArsipDocument $arsipDocument): ?array
    {
        if (! $this->arsipDocumentPolicy->view($user, $arsipDocument)) {
            return null;
        }

        $this->arsipDocumentRepository->incrementDownloadCount($arsipDocument);

        return [
            'download_name' => (string) $arsipDocument->original_name,
            'storage_path' => (string) $arsipDocument->file_path,
        ];
    }
}
