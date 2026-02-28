<?php

namespace App\Domains\Wilayah\Arsip\Actions;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CreateArsipDocumentAction
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository
    ) {
    }

    public function execute(array $payload, User $actor): ArsipDocument
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $payload['document_file'];
        $storedPath = $uploadedFile->store('arsip-documents', 'public');

        $isPublished = (bool) ($payload['is_published'] ?? false);

        return $this->arsipDocumentRepository->store([
            'title' => (string) $payload['title'],
            'description' => $payload['description'] ?? null,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $storedPath,
            'mime_type' => $uploadedFile->getClientMimeType(),
            'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
            'size_bytes' => (int) $uploadedFile->getSize(),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);
    }
}
