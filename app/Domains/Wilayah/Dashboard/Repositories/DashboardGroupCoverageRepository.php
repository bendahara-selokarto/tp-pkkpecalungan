<?php

namespace App\Domains\Wilayah\Dashboard\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DashboardGroupCoverageRepository implements DashboardGroupCoverageRepositoryInterface
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository,
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService
    ) {
    }

    public function buildBreakdownByDesaForGroup(User $user, string $groupKey, ?int $month = null): array
    {
        $groupModules = $this->roleMenuVisibilityService->modulesForGroup($groupKey);
        if ($groupModules === []) {
            return [];
        }

        return $this->buildBreakdownByDesaForModules($user, $groupModules, $month);
    }

    public function buildBreakdownByDesaForModules(User $user, array $moduleSlugs, ?int $month = null): array
    {
        if (! is_numeric($user->area_id)) {
            return [];
        }

        $areaId = (int) $user->area_id;
        $areaLevel = $user->relationLoaded('area')
            ? $user->area?->level
            : $this->areaRepository->getLevelById($areaId);

        if (
            ! $user->hasRoleForScope(ScopeLevel::KECAMATAN->value)
            || $areaLevel !== ScopeLevel::KECAMATAN->value
        ) {
            return [];
        }

        $desaAreas = $this->areaRepository->getDesaByKecamatan($areaId)
            ->map(static fn ($area): array => [
                'id' => (int) $area->id,
                'name' => (string) $area->name,
            ])
            ->values();
        if ($desaAreas->isEmpty()) {
            return [];
        }

        $modelBySlug = $this->breakdownModelMap();
        $requestedSlugs = collect($moduleSlugs)
            ->filter(static fn ($slug): bool => is_string($slug) && trim($slug) !== '')
            ->map(static fn (string $slug): string => strtolower(trim($slug)))
            ->filter(static fn (string $slug): bool => array_key_exists($slug, $modelBySlug))
            ->unique()
            ->values();
        if ($requestedSlugs->isEmpty()) {
            return [];
        }

        $desaIds = $desaAreas->pluck('id')->map(static fn ($id): int => (int) $id)->values();
        $countsBySlug = [];

        foreach ($requestedSlugs as $slug) {
            /** @var class-string<Model> $modelClass */
            $modelClass = $modelBySlug[$slug];
            $modelQuery = $modelClass::query()
                ->where('level', ScopeLevel::DESA->value)
                ->whereIn('area_id', $desaIds->all());

            if ($month !== null) {
                if ($modelClass === Activity::class) {
                    $modelQuery->whereMonth('activity_date', $month);
                } elseif ($this->supportsMonthFilterByCreatedAt($modelClass)) {
                    $modelQuery->whereMonth('created_at', $month);
                }
            }

            $countsBySlug[$slug] = $modelQuery
                ->selectRaw('area_id, COUNT(*) as total')
                ->groupBy('area_id')
                ->pluck('total', 'area_id')
                ->map(static fn ($total): int => (int) $total)
                ->all();
        }

        return $desaAreas
            ->map(function (array $desa) use ($requestedSlugs, $countsBySlug): array {
                $perModule = [];
                $total = 0;

                foreach ($requestedSlugs as $slug) {
                    $count = (int) ($countsBySlug[$slug][(int) $desa['id']] ?? 0);
                    $perModule[$slug] = $count;
                    $total += $count;
                }

                return [
                    'desa_id' => (int) $desa['id'],
                    'desa_name' => (string) $desa['name'],
                    'total' => $total,
                    'per_module' => $perModule,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, class-string<Model>>
     */
    private function breakdownModelMap(): array
    {
        return [
            'activities' => Activity::class,
            'agenda-surat' => AgendaSurat::class,
            'anggota-tim-penggerak' => AnggotaTimPenggerak::class,
            'kader-khusus' => KaderKhusus::class,
            'buku-keuangan' => BukuKeuangan::class,
            'inventaris' => Inventaris::class,
            'data-warga' => DataWarga::class,
            'data-kegiatan-warga' => DataKegiatanWarga::class,
            'data-keluarga' => DataKeluarga::class,
            'data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => DataPemanfaatanTanahPekaranganHatinyaPkk::class,
            'data-industri-rumah-tangga' => DataIndustriRumahTangga::class,
            'data-pelatihan-kader' => DataPelatihanKader::class,
            'warung-pkk' => WarungPkk::class,
            'taman-bacaan' => TamanBacaan::class,
            'koperasi' => Koperasi::class,
            'kejar-paket' => KejarPaket::class,
            'posyandu' => Posyandu::class,
            'simulasi-penyuluhan' => SimulasiPenyuluhan::class,
            'catatan-keluarga' => DataWarga::class,
            'bkl' => Bkl::class,
            'bkr' => Bkr::class,
            'paar' => Paar::class,
        ];
    }

    /**
     * @param class-string<Model> $modelClass
     */
    private function supportsMonthFilterByCreatedAt(string $modelClass): bool
    {
        /** @var Model $model */
        $model = new $modelClass();

        if (! $model->usesTimestamps()) {
            return false;
        }

        return Schema::hasColumn($model->getTable(), 'created_at');
    }
}
