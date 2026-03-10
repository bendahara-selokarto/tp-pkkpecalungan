<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Repositories;

use App\Domains\Wilayah\ProgramPrioritas\DTOs\ProgramPrioritasData;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritasJadwalBulan;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritasSumberDana;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProgramPrioritasRepository implements ProgramPrioritasRepositoryInterface
{
    public function store(ProgramPrioritasData $data): ProgramPrioritas
    {
        return DB::transaction(function () use ($data): ProgramPrioritas {
            $programPrioritas = ProgramPrioritas::create([
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
                'tahun_anggaran' => $data->tahun_anggaran,
            ]);

            $this->syncNormalizedRelations($programPrioritas, $data);

            return $programPrioritas;
        });
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?int $creatorIdFilter = null): Collection
    {
        return ProgramPrioritas::query()
            ->with(['jadwalBulans', 'sumberDanas'])
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('id')
            ->get();
    }

    public function paginateByLevelAndArea(
        string $level,
        int $areaId,
        int $tahunAnggaran,
        int $perPage,
        ?int $creatorIdFilter = null
    ): LengthAwarePaginator {
        return ProgramPrioritas::query()
            ->with(['jadwalBulans', 'sumberDanas'])
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): ProgramPrioritas
    {
        return ProgramPrioritas::query()
            ->with(['jadwalBulans', 'sumberDanas'])
            ->findOrFail($id);
    }

    public function update(ProgramPrioritas $programPrioritas, ProgramPrioritasData $data): ProgramPrioritas
    {
        return DB::transaction(function () use ($programPrioritas, $data): ProgramPrioritas {
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
                'tahun_anggaran' => $data->tahun_anggaran,
            ]);

            $this->syncNormalizedRelations($programPrioritas, $data);

            return $programPrioritas;
        });
    }

    public function delete(ProgramPrioritas $programPrioritas): void
    {
        $programPrioritas->delete();
    }

    private function syncNormalizedRelations(ProgramPrioritas $programPrioritas, ProgramPrioritasData $data): void
    {
        $this->syncJadwalBulans($programPrioritas, $data);
        $this->syncSumberDanas($programPrioritas, $data);
    }

    private function syncJadwalBulans(ProgramPrioritas $programPrioritas, ProgramPrioritasData $data): void
    {
        ProgramPrioritasJadwalBulan::query()
            ->where('program_prioritas_id', $programPrioritas->id)
            ->delete();

        $now = now();
        $rows = [];

        for ($month = 1; $month <= 12; $month++) {
            $flag = $data->{"jadwal_bulan_{$month}"};
            if (! $flag) {
                continue;
            }

            $rows[] = [
                'program_prioritas_id' => $programPrioritas->id,
                'month' => $month,
                'level' => $programPrioritas->level,
                'area_id' => $programPrioritas->area_id,
                'created_by' => $programPrioritas->created_by,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            ProgramPrioritasJadwalBulan::query()->insert($rows);
        }
    }

    private function syncSumberDanas(ProgramPrioritas $programPrioritas, ProgramPrioritasData $data): void
    {
        ProgramPrioritasSumberDana::query()
            ->where('program_prioritas_id', $programPrioritas->id)
            ->delete();

        $now = now();
        $rows = [];

        $sourceMap = [
            'pusat' => $data->sumber_dana_pusat,
            'apbd' => $data->sumber_dana_apbd,
            'swd' => $data->sumber_dana_swd,
            'bant' => $data->sumber_dana_bant,
        ];

        foreach ($sourceMap as $source => $flag) {
            if (! $flag) {
                continue;
            }

            $rows[] = [
                'program_prioritas_id' => $programPrioritas->id,
                'source' => $source,
                'level' => $programPrioritas->level,
                'area_id' => $programPrioritas->area_id,
                'created_by' => $programPrioritas->created_by,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            ProgramPrioritasSumberDana::query()->insert($rows);
        }
    }
}
