<?php

namespace App\Domains\Wilayah\Activities\Services;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityScopeService
{
    /**
     * @var array<string, list<string>>
     */
    private const ROLE_SCOPED_ACTIVITY_ROLES_BY_LEVEL = [
        ScopeLevel::DESA->value => [
            'desa-pokja-i',
            'desa-pokja-ii',
            'desa-pokja-iii',
            'desa-pokja-iv',
        ],
    ];

    /**
     * @var list<string>
     */
    private const ROLE_SCOPED_ACTIVITY_BYPASS_ROLES = [
        'super-admin',
        'admin-desa',
        'admin-kecamatan',
    ];

    public function __construct(
        private readonly UserAreaContextService $userAreaContextService
    ) {
    }

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
     * @return list<string>
     */
    public function resolveRoleScopedActivityRoles(User $user, string $level): array
    {
        $allowedRoles = self::ROLE_SCOPED_ACTIVITY_ROLES_BY_LEVEL[$level] ?? [];
        if ($allowedRoles === []) {
            return [];
        }

        $userRoles = $user->getRoleNames()->all();

        return array_values(array_intersect($allowedRoles, $userRoles));
    }

    public function requiresRoleScopedActivityFilter(User $user, string $level): bool
    {
        if ($user->hasAnyRole(self::ROLE_SCOPED_ACTIVITY_BYPASS_ROLES)) {
            return false;
        }

        return $this->resolveRoleScopedActivityRoles($user, $level) !== [];
    }

    public function canAccessRoleScopedActivity(User $user, Activity $activity, string $level): bool
    {
        if (! $this->requiresRoleScopedActivityFilter($user, $level)) {
            return true;
        }

        $allowedRoles = $this->resolveRoleScopedActivityRoles($user, $level);
        if ($allowedRoles === []) {
            return false;
        }

        $activity->loadMissing('creator.roles');
        $creator = $activity->creator;
        if (! $creator) {
            return false;
        }

        $creatorRoles = $creator->getRoleNames()->all();

        return array_intersect($allowedRoles, $creatorRoles) !== [];
    }

    public function authorizeRoleScopedActivity(User $user, Activity $activity, string $level): Activity
    {
        if (! $this->canAccessRoleScopedActivity($user, $activity, $level)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
    }

    public function isSameLevelAndArea(Activity $activity, string $level, int $areaId): bool
    {
        return $activity->level === $level && $activity->area_id === $areaId;
    }

    public function isDesaInKecamatan(Activity $activity, int $kecamatanAreaId): bool
    {
        return $activity->level === ScopeLevel::DESA->value
            && $activity->area?->level === ScopeLevel::DESA->value
            && $activity->area?->parent_id === $kecamatanAreaId;
    }

    public function canView(User $user, Activity $activity): bool
    {
        if ($user->hasRoleForScope(ScopeLevel::DESA->value)) {
            if (! $this->canAccessLevel($user, ScopeLevel::DESA->value)) {
                return false;
            }

            return $this->isSameLevelAndArea($activity, ScopeLevel::DESA->value, (int) $user->area_id);
        }

        if ($user->hasRoleForScope(ScopeLevel::KECAMATAN->value)) {
            if (! $this->canAccessLevel($user, ScopeLevel::KECAMATAN->value)) {
                return false;
            }

            if ($activity->level === ScopeLevel::KECAMATAN->value) {
                return $this->isSameLevelAndArea($activity, ScopeLevel::KECAMATAN->value, (int) $user->area_id);
            }

            if ($activity->level === ScopeLevel::DESA->value) {
                return $this->isDesaInKecamatan($activity, (int) $user->area_id);
            }
        }

        return false;
    }

    public function canUpdate(User $user, Activity $activity): bool
    {
        if ($user->hasRoleForScope(ScopeLevel::DESA->value)) {
            if (! $this->canAccessLevel($user, ScopeLevel::DESA->value)) {
                return false;
            }

            return $this->isSameLevelAndArea($activity, ScopeLevel::DESA->value, (int) $user->area_id);
        }

        if ($user->hasRoleForScope(ScopeLevel::KECAMATAN->value)) {
            if (! $this->canAccessLevel($user, ScopeLevel::KECAMATAN->value)) {
                return false;
            }

            return $this->isSameLevelAndArea($activity, ScopeLevel::KECAMATAN->value, (int) $user->area_id);
        }

        return false;
    }

    public function canPrint(User $user, Activity $activity): bool
    {
        return $this->canView($user, $activity);
    }

    public function authorizeSameLevelAndArea(Activity $activity, string $level, int $areaId): Activity
    {
        if (! $this->isSameLevelAndArea($activity, $level, $areaId)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
    }

    public function authorizeDesaInKecamatan(Activity $activity, int $kecamatanAreaId): Activity
    {
        if (! $this->isDesaInKecamatan($activity, $kecamatanAreaId)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
    }
}
