<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Services;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuNotulenRapatScopeService
{
    /**
     * @var array<string, string>
     */
    private const ROLE_TO_GROUP_MAP = [
        RoleScopeMatrix::ROLE_SEKRETARIS_DESA => 'sekretaris-tpk',
        RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN => 'sekretaris-tpk',
        RoleScopeMatrix::ROLE_POKJA_1_DESA => 'pokja-i',
        RoleScopeMatrix::ROLE_POKJA_1_KECAMATAN => 'pokja-i',
        RoleScopeMatrix::ROLE_POKJA_2_DESA => 'pokja-ii',
        RoleScopeMatrix::ROLE_POKJA_2_KECAMATAN => 'pokja-ii',
        RoleScopeMatrix::ROLE_POKJA_3_DESA => 'pokja-iii',
        RoleScopeMatrix::ROLE_POKJA_3_KECAMATAN => 'pokja-iii',
        RoleScopeMatrix::ROLE_POKJA_4_DESA => 'pokja-iv',
        RoleScopeMatrix::ROLE_POKJA_4_KECAMATAN => 'pokja-iv',
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

    public function canView(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        if (! $this->canAccessLevel($user, $bukuNotulenRapat->level)) {
            return false;
        }

        return (int) $bukuNotulenRapat->area_id === (int) $user->area_id
            && (int) $bukuNotulenRapat->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessGroup($user);
    }

    public function canUpdate(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        return $this->canView($user, $bukuNotulenRapat);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(BukuNotulenRapat $bukuNotulenRapat, string $level, int $areaId, int $tahunAnggaran): BukuNotulenRapat
    {
        if (
            $bukuNotulenRapat->level !== $level
            || (int) $bukuNotulenRapat->area_id !== $areaId
            || (int) $bukuNotulenRapat->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuNotulenRapat;
    }
    /**
     * @return list<string>
     */
    public function resolveGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_GROUP_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'buku-notulen-rapat', $groups);
    }

    public function requiresGroupFilter(User $user): bool
    {
        return ! $user->hasAnyRole(self::ROLE_SCOPED_BYPASS_ROLES);
    }

    public function canAccessGroup(User $user): bool
    {
        if (! $this->requiresGroupFilter($user)) {
            return true;
        }

        return $this->resolveGroupsForUser($user) !== [];
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
