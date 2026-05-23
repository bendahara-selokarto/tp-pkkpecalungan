<?php

namespace App\Domains\Wilayah\FotoKegiatan\Actions;

use App\Domains\Wilayah\FotoKegiatan\DTOs\FotoKegiatanData;
use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use App\Domains\Wilayah\FotoKegiatan\Repositories\FotoKegiatanRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateFotoKegiatanAction
{
    public function __construct(
        private readonly FotoKegiatanRepositoryInterface $fotoKegiatanRepository
    ) {
    }

    public function execute(FotoKegiatan $fotoKegiatan, array $payload): FotoKegiatan
    {
        $storedPath = $fotoKegiatan->image_path;

        if (isset($payload['image_upload']) && $payload['image_upload'] instanceof UploadedFile) {
            if ($fotoKegiatan->image_path) {
                Storage::disk('public')->delete($fotoKegiatan->image_path);
            }
            $storedPath = $payload['image_upload']->store('foto-kegiatans', 'public');
        }

        $tahunAnggaran = isset($payload['activity_date']) 
            ? (int) date('Y', strtotime($payload['activity_date']))
            : $fotoKegiatan->tahun_anggaran;

        $data = FotoKegiatanData::fromArray([
            'activity_date' => $payload['activity_date'],
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'image_path' => $storedPath,
            'level' => $fotoKegiatan->level,
            'area_id' => $fotoKegiatan->area_id,
            'group' => $fotoKegiatan->group,
            'created_by' => $fotoKegiatan->created_by,
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->fotoKegiatanRepository->update($fotoKegiatan, $data);
    }
}
