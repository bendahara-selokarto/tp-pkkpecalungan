<?php

namespace App\Domains\Wilayah\Arsip\Actions;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateArsipDocumentAction
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository,
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

    public function execute(array $payload, User $actor): ArsipDocument
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $payload['document_file'];
        $storedPath = $uploadedFile->store('arsip-documents', 'public');

        $areaId = is_numeric($actor->area_id) ? (int) $actor->area_id : 0;
        $areaLevel = $areaId > 0 ? $this->areaRepository->getLevelById($areaId) : null;

        if (! in_array($areaLevel, ['desa', 'kecamatan'], true)) {
            throw new HttpException(403, 'Area pengguna belum valid untuk mengelola arsip.');
        }

        $isGlobal = $actor->hasRole('super-admin');

        return $this->arsipDocumentRepository->store([
            'title' => (string) $payload['title'],
            'description' => $payload['description'] ?? null,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $storedPath,
            'mime_type' => $uploadedFile->getClientMimeType(),
            'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
            'size_bytes' => (int) $uploadedFile->getSize(),
            'is_global' => $isGlobal,
            'level' => $areaLevel,
            'area_id' => $areaId,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);
    }
}
