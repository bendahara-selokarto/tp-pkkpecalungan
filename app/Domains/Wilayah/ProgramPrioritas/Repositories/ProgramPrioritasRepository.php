<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Repositories;

use App\Domains\Wilayah\ProgramPrioritas\DTOs\ProgramPrioritasData;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
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

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return ProgramPrioritas::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
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
