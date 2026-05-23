<?php

namespace App\Domains\Wilayah\FotoKegiatan\Services;

use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FotoKegiatanScopeService
{
    /**
     * @var array<string, string>
     */
    private const ROLE_TO_GROUP_MAP = [
        RoleScopeMatrix::ROLE_POKJA_2_DESA => 'pokja-ii',
        RoleScopeMatrix::ROLE_POKJA_3_DESA => 'pokja-iii',
        RoleScopeMatrix::ROLE_POKJA_4_DESA => 'pokja-iv',
        RoleScopeMatrix::ROLE_POKJA_2_KECAMATAN => 'pokja-ii',
        RoleScopeMatrix::ROLE_POKJA_3_KECAMATAN => 'pokja-iii',
        RoleScopeMatrix::ROLE_POKJA_4_KECAMATAN => 'pokja-iv',
    ];

    /**
     * @var list<string>
     */
    private const ROLE_SCOPED_BYPASS_ROLES = [
        RoleScopeMatrix::ROLE_SUPER_ADMIN,
        RoleScopeMatrix::ROLE_ADMIN_DESA,
        RoleScopeMatrix::ROLE_ADMIN_KECAMATAN,
        RoleScopeMatrix::ROLE_SEKRETARIS_DESA,
        RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN,
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

    public function canView(User $user, FotoKegiatan $fotoKegiatan): bool
    {
        if (! $this->canAccessLevel($user, $fotoKegiatan->level)) {
            return false;
        }

        return (int) $fotoKegiatan->area_id === (int) $user->area_id
            && (int) $fotoKegiatan->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessGroup($user, $fotoKegiatan);
    }

    public function canUpdate(User $user, FotoKegiatan $fotoKegiatan): bool
    {
        return $this->canView($user, $fotoKegiatan);
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

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'foto-kegiatan', $groups);
    }

    public function requiresGroupFilter(User $user): bool
    {
        return ! $user->hasAnyRole(self::ROLE_SCOPED_BYPASS_ROLES);
    }

    public function canAccessGroup(User $user, FotoKegiatan $fotoKegiatan): bool
    {
        if (! $this->requiresGroupFilter($user)) {
            return true;
        }

        return in_array((string) $fotoKegiatan->group, $this->resolveGroupsForUser($user), true);
    }

    public function authorizeGroup(User $user, FotoKegiatan $fotoKegiatan): FotoKegiatan
    {
        if (! $this->canAccessGroup($user, $fotoKegiatan)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $fotoKegiatan;
    }
}
