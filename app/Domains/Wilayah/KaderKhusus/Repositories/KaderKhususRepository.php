<?php

namespace App\Domains\Wilayah\KaderKhusus\Repositories;

use App\Domains\Wilayah\KaderKhusus\DTOs\KaderKhususData;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class KaderKhususRepository implements KaderKhususRepositoryInterface
{
    public function __construct(
        private readonly KaderKhususScopeService $kaderKhususScopeService
    ) {
    }

    public function store(KaderKhususData $data): KaderKhusus
    {
        return KaderKhusus::create([
            'nama' => $data->nama,
            'jenis_kelamin' => $data->jenis_kelamin,
            'tempat_lahir' => $data->tempat_lahir,
            'tanggal_lahir' => $data->tanggal_lahir,
            'status_perkawinan' => $data->status_perkawinan,
            'alamat' => $data->alamat,
            'pendidikan' => $data->pendidikan,
            'jenis_kader_khusus' => $data->jenis_kader_khusus,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'group' => $data->group,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?User $actor = null, ?int $creatorIdFilter = null): LengthAwarePaginator
    {
        return $this->applyKaderKhususGroupFilter(
            KaderKhusus::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->where('tahun_anggaran', $tahunAnggaran),
            $actor
        )
            ->when(is_int($creatorIdFilter), static fn (Builder $query) => $query->where('created_by', $creatorIdFilter))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?User $actor = null, ?int $creatorIdFilter = null): Collection
    {
        return $this->applyKaderKhususGroupFilter(
            KaderKhusus::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->where('tahun_anggaran', $tahunAnggaran),
            $actor
        )
            ->when(is_int($creatorIdFilter), static fn (Builder $query) => $query->where('created_by', $creatorIdFilter))
            ->latest('id')
            ->get();
    }

    private function applyKaderKhususGroupFilter(Builder $query, ?User $actor): Builder
    {
        if (! $actor instanceof User) {
            return $query;
        }

        if (! $this->kaderKhususScopeService->requiresKaderKhususGroupFilter($actor)) {
            return $query;
        }

        $allowedGroups = $this->kaderKhususScopeService->resolveKaderKhususGroupsForUser($actor);
        if ($allowedGroups === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('group', $allowedGroups);
    }

    public function find(int $id): KaderKhusus
    {
        return KaderKhusus::findOrFail($id);
    }

    public function update(KaderKhusus $kaderKhusus, KaderKhususData $data): KaderKhusus
    {
        $kaderKhusus->update([
            'nama' => $data->nama,
            'jenis_kelamin' => $data->jenis_kelamin,
            'tempat_lahir' => $data->tempat_lahir,
            'tanggal_lahir' => $data->tanggal_lahir,
            'status_perkawinan' => $data->status_perkawinan,
            'alamat' => $data->alamat,
            'pendidikan' => $data->pendidikan,
            'jenis_kader_khusus' => $data->jenis_kader_khusus,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $kaderKhusus;
    }

    public function delete(KaderKhusus $kaderKhusus): void
    {
        $kaderKhusus->delete();
    }
}


