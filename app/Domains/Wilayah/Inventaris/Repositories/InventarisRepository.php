<?php

namespace App\Domains\Wilayah\Inventaris\Repositories;

use App\Domains\Wilayah\Inventaris\DTOs\InventarisData;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class InventarisRepository implements InventarisRepositoryInterface
{
    public function __construct(
        private readonly InventarisScopeService $inventarisScopeService
    ) {
    }

    public function store(InventarisData $data): Inventaris
    {
        return Inventaris::create([
            'name' => $data->name,
            'asal_barang' => $data->asal_barang,
            'description' => $data->description,
            'keterangan' => $data->keterangan,
            'quantity' => $data->quantity,
            'unit' => $data->unit,
            'tanggal_penerimaan' => $data->tanggal_penerimaan,
            'tempat_penyimpanan' => $data->tempat_penyimpanan,
            'condition' => $data->condition,
            'level' => $data->level,
            'group' => $data->group,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?User $actor = null): LengthAwarePaginator
    {
        return $this->applyInventarisGroupFilter(
            Inventaris::query()
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
        return $this->applyInventarisGroupFilter(
            Inventaris::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->where('tahun_anggaran', $tahunAnggaran),
            $actor
        )
            ->latest('id')
            ->get();
    }

    private function applyInventarisGroupFilter(Builder $query, ?User $actor): Builder
    {
        if (! $actor instanceof User) {
            return $query;
        }

        if (! $this->inventarisScopeService->requiresInventarisGroupFilter($actor)) {
            return $query;
        }

        $allowedGroups = $this->inventarisScopeService->resolveInventarisGroupsForUser($actor);
        if ($allowedGroups === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('group', $allowedGroups);
    }

    public function find(int $id): Inventaris
    {
        return Inventaris::findOrFail($id);
    }

    public function update(Inventaris $inventaris, InventarisData $data): Inventaris
    {
        $inventaris->update([
            'name' => $data->name,
            'asal_barang' => $data->asal_barang,
            'description' => $data->description,
            'keterangan' => $data->keterangan,
            'quantity' => $data->quantity,
            'unit' => $data->unit,
            'tanggal_penerimaan' => $data->tanggal_penerimaan,
            'tempat_penyimpanan' => $data->tempat_penyimpanan,
            'condition' => $data->condition,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $inventaris;
    }

    public function delete(Inventaris $inventaris): void
    {
        $inventaris->delete();
    }
}


