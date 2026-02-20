<?php

namespace App\Domains\Wilayah\KejarPaket\Actions;

use App\Domains\Wilayah\KejarPaket\DTOs\KejarPaketData;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepositoryInterface;
use App\Domains\Wilayah\KejarPaket\Services\KejarPaketScopeService;

class CreateScopedKejarPaketAction
{
    public function __construct(
        private readonly KejarPaketRepositoryInterface $kejarPaketRepository,
        private readonly KejarPaketScopeService $kejarPaketScopeService
    ) {
    }

    public function execute(array $payload, string $level): KejarPaket
    {
        $data = KejarPaketData::fromArray([
            'nama_kejar_paket' => $payload['nama_kejar_paket'],
            'jenis_kejar_paket' => $payload['jenis_kejar_paket'],
            'jumlah_warga_belajar_l' => $payload['jumlah_warga_belajar_l'],
            'jumlah_warga_belajar_p' => $payload['jumlah_warga_belajar_p'],
            'jumlah_pengajar_l' => $payload['jumlah_pengajar_l'],
            'jumlah_pengajar_p' => $payload['jumlah_pengajar_p'],
            'level' => $level,
            'area_id' => $this->kejarPaketScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->kejarPaketRepository->store($data);
    }
}





