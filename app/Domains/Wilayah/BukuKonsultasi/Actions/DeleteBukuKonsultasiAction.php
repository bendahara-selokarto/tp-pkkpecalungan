<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Actions;

use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use App\Domains\Wilayah\BukuKonsultasi\Repositories\BukuKonsultasiRepositoryInterface;

class DeleteBukuKonsultasiAction
{
    public function __construct(
        private readonly BukuKonsultasiRepositoryInterface $bukuKonsultasiRepository
    ) {
    }

    public function execute(BukuKonsultasi $bukuKonsultasi): void
    {
        $this->bukuKonsultasiRepository->delete($bukuKonsultasi);
    }
}
