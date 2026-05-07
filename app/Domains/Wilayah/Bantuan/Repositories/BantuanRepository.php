<?php

namespace App\Domains\Wilayah\Bantuan\Repositories;

use App\Domains\Wilayah\Bantuan\DTOs\BantuanData;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BantuanRepository implements BantuanRepositoryInterface
{
    public function __construct(
        private readonly BantuanScopeService $bantuanScopeService
    ) {}

    public function store(BantuanData $data): Bantuan
    {
        return Bantuan::create([
            'name' => $data->lokasi_penerima,
            'category' => $data->jenis_bantuan,
            'description' => $data->keterangan,
            'source' => $data->asal_bantuan,
            'amount' => $data->jumlah,
            'received_date' => $data->tanggal,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'group' => $data->group,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?User $actor = null): LengthAwarePaginator
    {
        return $this->applyBantuanGroupFilter(
            Bantuan::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->where('tahun_anggaran', $tahunAnggaran),
            $actor
        )
            ->latest('received_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?User $actor = null): Collection
    {
        return $this->applyBantuanGroupFilter(
            Bantuan::query()
                ->where('level', $level)
                ->where('area_id', $areaId)
                ->where('tahun_anggaran', $tahunAnggaran),
            $actor
        )
            ->latest('received_date')
            ->latest('id')
            ->get();
    }

    private function applyBantuanGroupFilter(Builder $query, ?User $actor): Builder
    {
        if (! $actor instanceof User) {
            return $query;
        }

        if (! $this->bantuanScopeService->requiresBantuanGroupFilter($actor)) {
            return $query;
        }

        $allowedGroups = $this->bantuanScopeService->resolveBantuanGroupsForUser($actor);
        if ($allowedGroups === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('group', $allowedGroups);
    }

    public function find(int $id): Bantuan
    {
        return Bantuan::findOrFail($id);
    }

    public function update(Bantuan $bantuan, BantuanData $data): Bantuan
    {
        $bantuan->update([
            'name' => $data->lokasi_penerima,
            'category' => $data->jenis_bantuan,
            'description' => $data->keterangan,
            'source' => $data->asal_bantuan,
            'amount' => $data->jumlah,
            'received_date' => $data->tanggal,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $bantuan;
    }

    public function delete(Bantuan $bantuan): void
    {
        $bantuan->delete();
    }
}
