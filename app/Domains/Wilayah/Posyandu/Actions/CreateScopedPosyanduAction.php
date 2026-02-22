<?php

namespace App\Domains\Wilayah\Posyandu\Actions;

use App\Domains\Wilayah\Posyandu\DTOs\PosyanduData;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Posyandu\Repositories\PosyanduRepositoryInterface;
use App\Domains\Wilayah\Posyandu\Services\PosyanduScopeService;

class CreateScopedPosyanduAction
{
    public function __construct(
        private readonly PosyanduRepositoryInterface $posyanduRepository,
        private readonly PosyanduScopeService $posyanduScopeService
    ) {
    }

    public function execute(array $payload, string $level): Posyandu
    {
        $data = PosyanduData::fromArray([
            'nama_posyandu' => $payload['nama_posyandu'],
            'nama_pengelola' => $payload['nama_pengelola'],
            'nama_sekretaris' => $payload['nama_sekretaris'],
            'jenis_posyandu' => $payload['jenis_posyandu'],
            'jumlah_kader' => $payload['jumlah_kader'],
            'jenis_kegiatan' => $payload['jenis_kegiatan'],
            'frekuensi_layanan' => $payload['frekuensi_layanan'],
            'jumlah_pengunjung_l' => $payload['jumlah_pengunjung_l'],
            'jumlah_pengunjung_p' => $payload['jumlah_pengunjung_p'],
            'jumlah_petugas_l' => $payload['jumlah_petugas_l'],
            'jumlah_petugas_p' => $payload['jumlah_petugas_p'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $level,
            'area_id' => $this->posyanduScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->posyanduRepository->store($data);
    }
}





