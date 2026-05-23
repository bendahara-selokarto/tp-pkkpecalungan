<?php

namespace App\Domains\Wilayah\FotoKegiatan\Actions;

use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use App\Domains\Wilayah\FotoKegiatan\Repositories\FotoKegiatanRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class DeleteFotoKegiatanAction
{
    public function __construct(
        private readonly FotoKegiatanRepositoryInterface $fotoKegiatanRepository
    ) {
    }

    public function execute(FotoKegiatan $fotoKegiatan): void
    {
        if ($fotoKegiatan->image_path) {
            Storage::disk('public')->delete($fotoKegiatan->image_path);
        }

        $this->fotoKegiatanRepository->delete($fotoKegiatan);
    }
}
