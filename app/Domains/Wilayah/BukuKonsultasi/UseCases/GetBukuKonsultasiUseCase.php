<?php

namespace App\Domains\Wilayah\BukuKonsultasi\UseCases;

use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use App\Domains\Wilayah\BukuKonsultasi\Repositories\BukuKonsultasiRepositoryInterface;
use App\Domains\Wilayah\BukuKonsultasi\Services\BukuKonsultasiScopeService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GetBukuKonsultasiUseCase
{
    public function __construct(
        private readonly BukuKonsultasiRepositoryInterface $bukuKonsultasiRepository,
        private readonly BukuKonsultasiScopeService $bukuKonsultasiScopeService
    ) {
    }

    public function execute(int $id, string $level): BukuKonsultasi
    {
        $bukuKonsultasi = $this->bukuKonsultasiRepository->findById($id);

        if (! $bukuKonsultasi || $bukuKonsultasi->level !== $level) {
            throw new HttpException(404, 'Buku konsultasi tidak ditemukan.');
        }

        return $bukuKonsultasi;
    }
}
