<?php

namespace App\Domains\Wilayah\Activities\Services;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityScopeService
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
        private readonly RoleBookGroupContextService $roleBookGroupContextService
    ) {}

    public function canAccessLevel(User $user, string $level): bool
    {
        return $this->userAreaContextService->canAccessLevel($user, $level);
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
        // Use the centralized job group resolution from RoleScopeMatrix
        $groups = [];
        $roleName = $user->role; // Assuming single role anchor for now
        
        if ($roleName) {
            $jobGroup = RoleScopeMatrix::resolveJobGroup($roleName);
            if ($jobGroup) {
                $groups[] = $jobGroup;
            }
        }

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
     * super_admin can see all groups.
     */
    public function requiresActivityGroupFilter(User $user): bool
    {
        if ($user->role === RoleScopeMatrix::ROLE_SUPER_ADMIN) {
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
        // super_admin bypass
        if ($user->role === RoleScopeMatrix::ROLE_SUPER_ADMIN) {
            return true;
        }

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
        if ($user->role === RoleScopeMatrix::ROLE_SUPER_ADMIN) {
            return true;
        }

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

    public function requireActiveBudgetYear(): int
    {
        return $this->activeBudgetYearContextService->requireForAuthenticatedUser();
    }
}
