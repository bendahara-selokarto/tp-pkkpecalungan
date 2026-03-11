<?php

namespace App\Domains\Wilayah\BkbKegiatan\Repositories;

use App\Domains\Wilayah\BkbKegiatan\DTOs\BkbKegiatanData;
use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BkbKegiatanRepositoryInterface
{
    public function store(BkbKegiatanData $data): BkbKegiatan;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection;

    public function find(int $id): BkbKegiatan;

    public function update(BkbKegiatan $bkbKegiatan, BkbKegiatanData $data): BkbKegiatan;

    public function delete(BkbKegiatan $bkbKegiatan): void;
}
