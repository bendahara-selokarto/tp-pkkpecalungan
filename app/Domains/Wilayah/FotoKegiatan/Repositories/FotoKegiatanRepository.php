<?php

namespace App\Domains\Wilayah\FotoKegiatan\Repositories;

use App\Domains\Wilayah\FotoKegiatan\DTOs\FotoKegiatanData;
use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FotoKegiatanRepository implements FotoKegiatanRepositoryInterface
{
    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?string $group = null): LengthAwarePaginator
    {
        $query = FotoKegiatan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran);

        if ($group !== null) {
            $query->where('group', $group);
        }

        return $query->latest('activity_date')
            ->latest('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?FotoKegiatan
    {
        return FotoKegiatan::query()->find($id);
    }

    public function store(FotoKegiatanData $data): FotoKegiatan
    {
        return FotoKegiatan::create([
            'activity_date' => $data->activity_date,
            'title' => $data->title,
            'description' => $data->description,
            'image_path' => $data->image_path,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'group' => $data->group,
            'created_by' => $data->created_by,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);
    }

    public function update(FotoKegiatan $fotoKegiatan, FotoKegiatanData $data): FotoKegiatan
    {
        $fotoKegiatan->update([
            'activity_date' => $data->activity_date,
            'title' => $data->title,
            'description' => $data->description,
            'image_path' => $data->image_path ?? $fotoKegiatan->image_path,
            'tahun_anggaran' => $data->tahun_anggaran ?? $fotoKegiatan->tahun_anggaran,
        ]);

        return $fotoKegiatan->refresh();
    }

    public function delete(FotoKegiatan $fotoKegiatan): void
    {
        $fotoKegiatan->delete();
    }
}
