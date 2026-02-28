<?php

namespace App\Domains\Wilayah\Arsip\Actions;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class DeleteArsipDocumentAction
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository
    ) {
    }

    public function execute(ArsipDocument $arsipDocument): void
    {
        $storedPath = $arsipDocument->file_path;

        $this->arsipDocumentRepository->delete($arsipDocument);

        if (is_string($storedPath) && $storedPath !== '') {
            Storage::disk('public')->delete($storedPath);
        }
    }
}
