<?php

namespace App\Domains\Wilayah\Activities\Services;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityScopeService
{
    /**
     * @var array<string, string>
     */
    private const ROLE_TO_GROUP_MAP = [
        'desa-pokja-i' => 'pokja-i',
        'desa-pokja-ii' => 'pokja-ii',
        'desa-pokja-iii' => 'pokja-iii',
        'desa-pokja-iv' => 'pokja-iv',
        'kecamatan-pokja-i' => 'pokja-i',
        'kecamatan-pokja-ii' => 'pokja-ii',
        'kecamatan-pokja-iii' => 'pokja-iii',
        'kecamatan-pokja-iv' => 'pokja-iv',
        'desa-sekretaris' => 'sekretaris-tpk',
        'kecamatan-sekretaris' => 'sekretaris-tpk',
    ];

    /**
     * @var list<string>
     */
    private const ROLE_SCOPED_ACTIVITY_BYPASS_ROLES = [
        'super-admin',
    ];

    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
        private readonly RoleBookGroupContextService $roleBookGroupContextService
    ) {}

    public function canAccessLevel(User $user, string $level): bool
    {
        return $this->userAreaContextService->canAccessLevel($user, $level);
    }

    public function canEnterModule(User $user): bool
    {
        return $this->userAreaContextService->canEnterModule($user);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function requireAuthenticatedUser(): User
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            throw new HttpException(403, 'Pengguna tidak terautentikasi.');
        }

        return $user;
    }

    /**
     * Resolve activity groups (pokja/group) that the user can access based on their roles.
     *
     * @return list<string>
     */
    public function resolveActivityGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_GROUP_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'activities', $groups);
    }

    public function requireActivityGroupForUser(User $user): string
    {
        $groups = $this->resolveActivityGroupsForUser($user);
        if ($groups === []) {
            throw new HttpException(403, 'Pengguna tidak memiliki jabatan buku kegiatan aktif.');
        }

        return $groups[0];
    }

    /**
     * Check if user needs activity group filtering based on their roles.
     * Only role-scoped users (sekretaris and pokja-i through pokja-iv) need filtering;
     * super-admin can see all groups.
     */
    public function requiresActivityGroupFilter(User $user): bool
    {
        if ($user->hasAnyRole(self::ROLE_SCOPED_ACTIVITY_BYPASS_ROLES)) {
            return false;
        }

        return true;
    }

    public function canAccessActivityGroup(User $user, Activity $activity): bool
    {
        if (! $this->requiresActivityGroupFilter($user)) {
            return true;
        }

        $allowedGroups = $this->resolveActivityGroupsForUser($user);

        return in_array((string) $activity->group, $allowedGroups, true);
    }

    public function authorizeActivityGroup(User $user, Activity $activity): Activity
    {
        if (! $this->canAccessActivityGroup($user, $activity)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
    }

    public function isSameLevelAreaAndBudgetYear(Activity $activity, string $level, int $areaId, int $tahunAnggaran): bool
    {
        return $activity->level === $level
            && (int) $activity->area_id === $areaId
            && (int) $activity->tahun_anggaran === $tahunAnggaran;
    }

    public function isDesaInKecamatanAndBudgetYear(Activity $activity, int $kecamatanAreaId, int $tahunAnggaran): bool
    {
        return $activity->level === ScopeLevel::DESA->value
            && $activity->area?->level === ScopeLevel::DESA->value
            && (int) $activity->area?->parent_id === $kecamatanAreaId
            && (int) $activity->tahun_anggaran === $tahunAnggaran;
    }

    public function canView(User $user, Activity $activity): bool
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);

        if ($user->hasRoleForScope(ScopeLevel::DESA->value)) {
            if (! $this->canAccessLevel($user, ScopeLevel::DESA->value)) {
                return false;
            }

            return $this->isSameLevelAreaAndBudgetYear($activity, ScopeLevel::DESA->value, (int) $user->area_id, $tahunAnggaran)
                && $this->canAccessActivityGroup($user, $activity);
        }

        if ($user->hasRoleForScope(ScopeLevel::KECAMATAN->value)) {
            if (! $this->canAccessLevel($user, ScopeLevel::KECAMATAN->value)) {
                return false;
            }

            if ($activity->level === ScopeLevel::KECAMATAN->value) {
                return $this->isSameLevelAreaAndBudgetYear($activity, ScopeLevel::KECAMATAN->value, (int) $user->area_id, $tahunAnggaran)
                    && $this->canAccessActivityGroup($user, $activity);
            }

            if ($activity->level === ScopeLevel::DESA->value) {
                return $this->isDesaInKecamatanAndBudgetYear($activity, (int) $user->area_id, $tahunAnggaran);
            }
        }

        return false;
    }

    public function canUpdate(User $user, Activity $activity): bool
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);

        if ($user->hasRoleForScope(ScopeLevel::DESA->value)) {
            if (! $this->canAccessLevel($user, ScopeLevel::DESA->value)) {
                return false;
            }

            return $this->isSameLevelAreaAndBudgetYear($activity, ScopeLevel::DESA->value, (int) $user->area_id, $tahunAnggaran)
                && $this->canAccessActivityGroup($user, $activity);
        }

        if ($user->hasRoleForScope(ScopeLevel::KECAMATAN->value)) {
            if (! $this->canAccessLevel($user, ScopeLevel::KECAMATAN->value)) {
                return false;
            }

            return $this->isSameLevelAreaAndBudgetYear($activity, ScopeLevel::KECAMATAN->value, (int) $user->area_id, $tahunAnggaran)
                && $this->canAccessActivityGroup($user, $activity);
        }

        return false;
    }

    public function canPrint(User $user, Activity $activity): bool
    {
        return $this->canView($user, $activity);
    }

    public function authorizeSameLevelAreaAndBudgetYear(Activity $activity, string $level, int $areaId, int $tahunAnggaran): Activity
    {
        if (! $this->isSameLevelAreaAndBudgetYear($activity, $level, $areaId, $tahunAnggaran)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
    }

    public function authorizeDesaInKecamatanAndBudgetYear(Activity $activity, int $kecamatanAreaId, int $tahunAnggaran): Activity
    {
        if (! $this->isDesaInKecamatanAndBudgetYear($activity, $kecamatanAreaId, $tahunAnggaran)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
    }

    public function requireActiveBudgetYear(): int
    {
        return $this->activeBudgetYearContextService->requireForAuthenticatedUser();
    }
}
