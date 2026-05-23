<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Repositories;

use App\Domains\Wilayah\BukuKonsultasi\DTOs\BukuKonsultasiData;
use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BukuKonsultasiRepositoryInterface
{
    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?string $group = null): LengthAwarePaginator;

    public function findById(int $id): ?BukuKonsultasi;

    public function store(BukuKonsultasiData $data): BukuKonsultasi;

    public function update(BukuKonsultasi $bukuKonsultasi, BukuKonsultasiData $data): BukuKonsultasi;

    public function delete(BukuKonsultasi $bukuKonsultasi): void;
}
