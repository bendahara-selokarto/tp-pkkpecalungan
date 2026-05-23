<?php

namespace App\Domains\Wilayah\FotoKegiatan\UseCases;

use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use App\Domains\Wilayah\FotoKegiatan\Repositories\FotoKegiatanRepositoryInterface;
use App\Domains\Wilayah\FotoKegiatan\Services\FotoKegiatanScopeService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GetFotoKegiatanUseCase
{
    public function __construct(
        private readonly FotoKegiatanRepositoryInterface $fotoKegiatanRepository,
        private readonly FotoKegiatanScopeService $fotoKegiatanScopeService
    ) {
    }

    public function execute(int $id, string $level): FotoKegiatan
    {
        $fotoKegiatan = $this->fotoKegiatanRepository->findById($id);

        if (! $fotoKegiatan) {
            throw new HttpException(404, 'Data foto kegiatan tidak ditemukan.');
        }

        if (! $this->fotoKegiatanScopeService->canView(auth()->user(), $fotoKegiatan) || $fotoKegiatan->level !== $level) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $fotoKegiatan;
    }
}
