<?php

namespace App\Domains\Wilayah\Simulasi\Actions;

use App\Domains\Wilayah\Simulasi\DTOs\BukuDaftarHadirSimulasiData;
use App\Domains\Wilayah\Simulasi\Models\BukuDaftarHadirSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuDaftarHadirSimulasiRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UpdateBukuDaftarHadirSimulasiAction
{
    public function __construct(
        private readonly BukuDaftarHadirSimulasiRepositoryInterface $repository
    ) {
    }

    public function execute(BukuDaftarHadirSimulasi $model, array $payload): BukuDaftarHadirSimulasi
    {
        $fileInfo = [
            'file_path' => $model->file_path,
            'original_name' => $model->original_name,
            'mime_type' => $model->mime_type,
            'extension' => $model->extension,
            'size_bytes' => (int) $model->size_bytes,
        ];

        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            if ($model->file_path) {
                Storage::disk('public')->delete($model->file_path);
            }

            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-daftar-hadir-simulasi', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];
        }

        $data = BukuDaftarHadirSimulasiData::fromArray([
            ...$payload,
            ...$fileInfo,
            'level' => $model->level,
            'area_id' => $model->area_id,
            'created_by' => $model->created_by,
            'tahun_anggaran' => $model->tahun_anggaran,
        ]);

        return $this->repository->update($model, $data);
    }
}
