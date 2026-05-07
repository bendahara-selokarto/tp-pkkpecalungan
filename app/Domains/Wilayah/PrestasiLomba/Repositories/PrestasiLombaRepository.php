<?php

namespace App\Domains\Wilayah\PrestasiLomba\Repositories;

use App\Domains\Wilayah\PrestasiLomba\DTOs\PrestasiLombaData;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Services\PrestasiLombaScopeService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PrestasiLombaRepository implements PrestasiLombaRepositoryInterface
{
    public function __construct(
        private readonly PrestasiLombaScopeService $prestasiLombaScopeService
    ) {}

    public function store(PrestasiLombaData $data): PrestasiLomba
    {
        return PrestasiLomba::create([
            'tahun' => $data->tahun,
            'jenis_lomba' => $data->jenis_lomba,
            'lokasi' => $data->lokasi,
            'prestasi_kecamatan' => $data->prestasi_kecamatan,
            'prestasi_kabupaten' => $data->prestasi_kabupaten,
            'prestasi_provinsi' => $data->prestasi_provinsi,
            'prestasi_nasional' => $data->prestasi_nasional,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'group' => $data->group,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?User $actor = null): LengthAwarePaginator
    {
        return $this->applyPrestasiGroupFilter(
            PrestasiLomba::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->where('tahun_anggaran', $tahunAnggaran),
            $actor
        )
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?User $actor = null): Collection
    {
        return $this->applyPrestasiGroupFilter(
            PrestasiLomba::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->where('tahun_anggaran', $tahunAnggaran),
            $actor
        )
            ->latest('id')
            ->get();
    }

    private function applyPrestasiGroupFilter(Builder $query, ?User $actor): Builder
    {
        if (! $actor instanceof User) {
            return $query;
        }

        if (! $this->prestasiLombaScopeService->requiresPrestasiGroupFilter($actor)) {
            return $query;
        }

        $allowedGroups = $this->prestasiLombaScopeService->resolvePrestasiGroupsForUser($actor);
        if ($allowedGroups === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('group', $allowedGroups);
    }

    public function find(int $id): PrestasiLomba
    {
        return PrestasiLomba::findOrFail($id);
    }

    public function update(PrestasiLomba $prestasiLomba, PrestasiLombaData $data): PrestasiLomba
    {
        $prestasiLomba->update([
            'tahun' => $data->tahun,
            'jenis_lomba' => $data->jenis_lomba,
            'lokasi' => $data->lokasi,
            'prestasi_kecamatan' => $data->prestasi_kecamatan,
            'prestasi_kabupaten' => $data->prestasi_kabupaten,
            'prestasi_provinsi' => $data->prestasi_provinsi,
            'prestasi_nasional' => $data->prestasi_nasional,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $prestasiLomba;
    }

    public function delete(PrestasiLomba $prestasiLomba): void
    {
        $prestasiLomba->delete();
    }
}
