<?php

namespace App\Domains\Wilayah\Arsip\Actions;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateArsipDocumentAction
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository
    ) {
    }

    public function execute(ArsipDocument $arsipDocument, array $payload, User $actor): ArsipDocument
    {
        $replacementFile = $payload['document_file'] ?? null;
        $replacementPath = null;
        $oldPath = $arsipDocument->file_path;

        if ($replacementFile instanceof UploadedFile) {
            $replacementPath = $replacementFile->store('arsip-documents', 'public');
        }

        $updatePayload = [
            'title' => (string) $payload['title'],
            'description' => $payload['description'] ?? null,
            'updated_by' => $actor->id,
        ];

        if ($replacementFile instanceof UploadedFile && is_string($replacementPath)) {
            $updatePayload = [
                ...$updatePayload,
                'original_name' => $replacementFile->getClientOriginalName(),
                'file_path' => $replacementPath,
                'mime_type' => $replacementFile->getClientMimeType(),
                'extension' => strtolower($replacementFile->getClientOriginalExtension()),
                'size_bytes' => (int) $replacementFile->getSize(),
            ];
        }

        try {
            $updatedDocument = $this->arsipDocumentRepository->update($arsipDocument, $updatePayload);
        } catch (Throwable $exception) {
            if (is_string($replacementPath)) {
                Storage::disk('public')->delete($replacementPath);
            }

            throw $exception;
        }

        if (is_string($replacementPath) && $oldPath !== $replacementPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $updatedDocument;
    }
}
