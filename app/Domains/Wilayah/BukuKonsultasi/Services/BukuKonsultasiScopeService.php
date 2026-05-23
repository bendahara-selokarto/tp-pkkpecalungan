<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Services;

use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuKonsultasiScopeService
{
    /**
     * @var array<string, string>
     */
    private const ROLE_TO_GROUP_MAP = [
        RoleScopeMatrix::ROLE_SEKRETARIS_DESA => 'sekretaris-tpk',
        RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN => 'sekretaris-tpk',
        RoleScopeMatrix::ROLE_POKJA_3_DESA => 'pokja-iii',
        RoleScopeMatrix::ROLE_POKJA_3_KECAMATAN => 'pokja-iii',
    ];

    /**
     * @var list<string>
     */
    private const ROLE_SCOPED_BYPASS_ROLES = [
        RoleScopeMatrix::ROLE_SUPER_ADMIN,
        RoleScopeMatrix::ROLE_ADMIN_DESA,
        RoleScopeMatrix::ROLE_ADMIN_KECAMATAN,
    ];

    public function __construct(
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
        private readonly UserAreaContextService $userAreaContextService,
        private readonly RoleBookGroupContextService $roleBookGroupContextService
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

    public function canView(User $user, BukuKonsultasi $bukuKonsultasi): bool
    {
        if (! $this->canAccessLevel($user, $bukuKonsultasi->level)) {
            return false;
        }

        return (int) $bukuKonsultasi->area_id === (int) $user->area_id
            && (int) $bukuKonsultasi->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessGroup($user, $bukuKonsultasi);
    }

    public function canUpdate(User $user, BukuKonsultasi $bukuKonsultasi): bool
    {
        return $this->canView($user, $bukuKonsultasi);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    /**
     * @return list<string>
     */
    public function resolveGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_GROUP_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'buku-konsultasi', $groups);
    }

    public function requiresGroupFilter(User $user): bool
    {
        return ! $user->hasAnyRole(self::ROLE_SCOPED_BYPASS_ROLES);
    }

    public function canAccessGroup(User $user, BukuKonsultasi $bukuKonsultasi): bool
    {
        if (! $this->requiresGroupFilter($user)) {
            return true;
        }

        return in_array((string) $bukuKonsultasi->group, $this->resolveGroupsForUser($user), true);
    }

    public function authorizeGroup(User $user, BukuKonsultasi $bukuKonsultasi): BukuKonsultasi
    {
        if (! $this->canAccessGroup($user, $bukuKonsultasi)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuKonsultasi;
    }
}
