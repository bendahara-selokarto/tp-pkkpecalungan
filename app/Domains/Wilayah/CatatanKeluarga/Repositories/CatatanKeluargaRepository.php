<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Repositories;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use Illuminate\Support\Collection;

class CatatanKeluargaRepository implements CatatanKeluargaRepositoryInterface
{
    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        $kegiatanByNama = DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->pluck('aktivitas', 'kegiatan');

        $aktivitasLabel = static function (Collection $items, string $kegiatan): string {
            return (bool) $items->get($kegiatan, false) ? 'Ya' : 'Tidak';
        };

        return DataWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get()
            ->values()
            ->map(fn (DataWarga $item, int $index) => [
                'id' => $item->id,
                'nomor_urut' => $index + 1,
                'nama_kepala_rumah_tangga' => $item->nama_kepala_keluarga,
                'jumlah_anggota_rumah_tangga' => $item->total_warga,
                'kerja_bakti' => $aktivitasLabel($kegiatanByNama, 'Kerja Bakti'),
                'rukun_kematian' => $aktivitasLabel($kegiatanByNama, 'Rukun Kematian'),
                'kegiatan_keagamaan' => $aktivitasLabel($kegiatanByNama, 'Kegiatan Keagamaan'),
                'jimpitan' => $aktivitasLabel($kegiatanByNama, 'Jimpitan'),
                'arisan' => $aktivitasLabel($kegiatanByNama, 'Arisan'),
                'lain_lain' => $aktivitasLabel($kegiatanByNama, 'Lain-Lain'),
                'keterangan' => $item->keterangan,
            ]);
    }
}

