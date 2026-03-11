<?php

namespace App\Domains\Wilayah\TutorKhusus\Repositories;

use App\Domains\Wilayah\TutorKhusus\DTOs\TutorKhususData;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TutorKhususRepository implements TutorKhususRepositoryInterface
{
    public function store(TutorKhususData $data): TutorKhusus
    {
        return TutorKhusus::create([
            'jenis_tutor' => $data->jenis_tutor,
            'jumlah_tutor' => $data->jumlah_tutor,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return TutorKhusus::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return TutorKhusus::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): TutorKhusus
    {
        return TutorKhusus::findOrFail($id);
    }

    public function update(TutorKhusus $tutorKhusus, TutorKhususData $data): TutorKhusus
    {
        $tutorKhusus->update([
            'jenis_tutor' => $data->jenis_tutor,
            'jumlah_tutor' => $data->jumlah_tutor,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $tutorKhusus;
    }

    public function delete(TutorKhusus $tutorKhusus): void
    {
        $tutorKhusus->delete();
    }
}
