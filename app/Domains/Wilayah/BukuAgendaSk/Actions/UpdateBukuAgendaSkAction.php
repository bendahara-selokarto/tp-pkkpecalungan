<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Actions;

use App\Domains\Wilayah\BukuAgendaSk\DTOs\BukuAgendaSkData;
use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\BukuAgendaSk\Repositories\BukuAgendaSkRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UpdateBukuAgendaSkAction
{
    public function __construct(
        private readonly BukuAgendaSkRepositoryInterface $bukuAgendaSkRepository
    ) {
    }

    public function execute(BukuAgendaSk $bukuAgendaSk, array $payload): BukuAgendaSk
    {
        $fileInfo = [
            'file_path' => $bukuAgendaSk->file_path,
            'original_name' => $bukuAgendaSk->original_name,
            'mime_type' => $bukuAgendaSk->mime_type,
            'extension' => $bukuAgendaSk->extension,
            'size_bytes' => (int) $bukuAgendaSk->size_bytes,
        ];

        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-agenda-sk', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];

            if ($bukuAgendaSk->file_path) {
                Storage::disk('public')->delete($bukuAgendaSk->file_path);
            }
        }

        $data = BukuAgendaSkData::fromArray([
            'nomor_sk' => $payload['nomor_sk'],
            'tanggal_sk' => $payload['tanggal_sk'],
            'kepada' => $payload['kepada'],
            'perihal' => $payload['perihal'],
            'tembusan' => $payload['tembusan'] ?? null,
            ...$fileInfo,
            'level' => $bukuAgendaSk->level,
            'area_id' => $bukuAgendaSk->area_id,
            'created_by' => $bukuAgendaSk->created_by,
            'tahun_anggaran' => $bukuAgendaSk->tahun_anggaran,
        ]);

        return $this->bukuAgendaSkRepository->update($bukuAgendaSk, $data);
    }
}
