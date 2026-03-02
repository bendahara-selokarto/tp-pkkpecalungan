<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Repositories;

use App\Domains\Wilayah\ProgramPrioritas\DTOs\ProgramPrioritasData;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProgramPrioritasRepository implements ProgramPrioritasRepositoryInterface
{
    public function store(ProgramPrioritasData $data): ProgramPrioritas
    {
        return ProgramPrioritas::create([
            'program' => $data->program,
            'prioritas_program' => $data->prioritas_program,
            'kegiatan' => $data->kegiatan,
            'sasaran_target' => $data->sasaran_target,
            'jadwal_bulan_1' => $data->jadwal_bulan_1,
            'jadwal_bulan_2' => $data->jadwal_bulan_2,
            'jadwal_bulan_3' => $data->jadwal_bulan_3,
            'jadwal_bulan_4' => $data->jadwal_bulan_4,
            'jadwal_bulan_5' => $data->jadwal_bulan_5,
            'jadwal_bulan_6' => $data->jadwal_bulan_6,
            'jadwal_bulan_7' => $data->jadwal_bulan_7,
            'jadwal_bulan_8' => $data->jadwal_bulan_8,
            'jadwal_bulan_9' => $data->jadwal_bulan_9,
            'jadwal_bulan_10' => $data->jadwal_bulan_10,
            'jadwal_bulan_11' => $data->jadwal_bulan_11,
            'jadwal_bulan_12' => $data->jadwal_bulan_12,
            'jadwal_i' => $data->jadwal_i,
            'jadwal_ii' => $data->jadwal_ii,
            'jadwal_iii' => $data->jadwal_iii,
            'jadwal_iv' => $data->jadwal_iv,
            'sumber_dana_pusat' => $data->sumber_dana_pusat,
            'sumber_dana_apbd' => $data->sumber_dana_apbd,
            'sumber_dana_swd' => $data->sumber_dana_swd,
            'sumber_dana_bant' => $data->sumber_dana_bant,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection
    {
        return ProgramPrioritas::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('id')
            ->get();
    }

    public function paginateByLevelAndArea(
        string $level,
        int $areaId,
        int $perPage,
        ?int $creatorIdFilter = null
    ): LengthAwarePaginator {
        return ProgramPrioritas::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): ProgramPrioritas
    {
        return ProgramPrioritas::findOrFail($id);
    }

    public function update(ProgramPrioritas $programPrioritas, ProgramPrioritasData $data): ProgramPrioritas
    {
        $programPrioritas->update([
            'program' => $data->program,
            'prioritas_program' => $data->prioritas_program,
            'kegiatan' => $data->kegiatan,
            'sasaran_target' => $data->sasaran_target,
            'jadwal_bulan_1' => $data->jadwal_bulan_1,
            'jadwal_bulan_2' => $data->jadwal_bulan_2,
            'jadwal_bulan_3' => $data->jadwal_bulan_3,
            'jadwal_bulan_4' => $data->jadwal_bulan_4,
            'jadwal_bulan_5' => $data->jadwal_bulan_5,
            'jadwal_bulan_6' => $data->jadwal_bulan_6,
            'jadwal_bulan_7' => $data->jadwal_bulan_7,
            'jadwal_bulan_8' => $data->jadwal_bulan_8,
            'jadwal_bulan_9' => $data->jadwal_bulan_9,
            'jadwal_bulan_10' => $data->jadwal_bulan_10,
            'jadwal_bulan_11' => $data->jadwal_bulan_11,
            'jadwal_bulan_12' => $data->jadwal_bulan_12,
            'jadwal_i' => $data->jadwal_i,
            'jadwal_ii' => $data->jadwal_ii,
            'jadwal_iii' => $data->jadwal_iii,
            'jadwal_iv' => $data->jadwal_iv,
            'sumber_dana_pusat' => $data->sumber_dana_pusat,
            'sumber_dana_apbd' => $data->sumber_dana_apbd,
            'sumber_dana_swd' => $data->sumber_dana_swd,
            'sumber_dana_bant' => $data->sumber_dana_bant,
            'keterangan' => $data->keterangan,
        ]);

        return $programPrioritas;
    }

    public function delete(ProgramPrioritas $programPrioritas): void
    {
        $programPrioritas->delete();
    }
}



