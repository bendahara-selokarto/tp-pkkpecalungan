<?php

namespace App\Domains\Wilayah\FotoKegiatan\Repositories;

use App\Domains\Wilayah\FotoKegiatan\DTOs\FotoKegiatanData;
use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FotoKegiatanRepositoryInterface
{
    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?string $group = null): LengthAwarePaginator;

    public function findById(int $id): ?FotoKegiatan;

    public function store(FotoKegiatanData $data): FotoKegiatan;

    public function update(FotoKegiatan $fotoKegiatan, FotoKegiatanData $data): FotoKegiatan;

    public function delete(FotoKegiatan $fotoKegiatan): void;
}
