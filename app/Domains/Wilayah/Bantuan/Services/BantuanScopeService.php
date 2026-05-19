<?php

namespace App\Domains\Wilayah\Bantuan\Services;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BantuanScopeService
{
    /**
     * @var array<string, string>
     */
    private const ROLE_TO_GROUP_MAP = [
        RoleScopeMatrix::ROLE_POKJA_1_DESA => 'pokja-i',
        RoleScopeMatrix::ROLE_POKJA_2_DESA => 'pokja-ii',
        RoleScopeMatrix::ROLE_POKJA_3_DESA => 'pokja-iii',
        RoleScopeMatrix::ROLE_POKJA_4_DESA => 'pokja-iv',
        RoleScopeMatrix::ROLE_POKJA_1_KECAMATAN => 'pokja-i',
        RoleScopeMatrix::ROLE_POKJA_2_KECAMATAN => 'pokja-ii',
        RoleScopeMatrix::ROLE_POKJA_3_KECAMATAN => 'pokja-iii',
        RoleScopeMatrix::ROLE_POKJA_4_KECAMATAN => 'pokja-iv',
        RoleScopeMatrix::ROLE_SEKRETARIS_DESA => 'sekretaris-tpk',
        RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN => 'sekretaris-tpk',
    ];

    /**
     * @var list<string>
     */
    private const ROLE_SCOPED_BANTUAN_BYPASS_ROLES = [
        RoleScopeMatrix::ROLE_SUPER_ADMIN,
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

    public function canView(User $user, Bantuan $bantuan): bool
    {
        if (! $this->canAccessLevel($user, $bantuan->level)) {
            return false;
        }

        return (int) $bantuan->area_id === (int) $user->area_id
            && (int) $bantuan->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessBantuanGroup($user, $bantuan);
    }

    public function canUpdate(User $user, Bantuan $bantuan): bool
    {
        return $this->canView($user, $bantuan);
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

    public function authorizeSameLevelAreaAndBudgetYear(Bantuan $bantuan, string $level, int $areaId, int $tahunAnggaran): Bantuan
    {
        if (
            $bantuan->level !== $level
            || (int) $bantuan->area_id !== $areaId
            || (int) $bantuan->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bantuan;
    }

    /**
     * @return list<string>
     */
    public function resolveBantuanGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_GROUP_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'bantuans', $groups);
    }

    public function requireBantuanGroupForUser(User $user): string
    {
        $groups = $this->resolveBantuanGroupsForUser($user);
        if ($groups === []) {
            throw new HttpException(403, 'Pengguna tidak memiliki jabatan buku bantuan aktif.');
        }

        return $groups[0];
    }

    public function requiresBantuanGroupFilter(User $user): bool
    {
        return ! $user->hasAnyRole(self::ROLE_SCOPED_BANTUAN_BYPASS_ROLES);
    }

    public function canAccessBantuanGroup(User $user, Bantuan $bantuan): bool
    {
        if (! $this->requiresBantuanGroupFilter($user)) {
            return true;
        }

        return in_array((string) $bantuan->group, $this->resolveBantuanGroupsForUser($user), true);
    }

    public function authorizeBantuanGroup(User $user, Bantuan $bantuan): Bantuan
    {
        if (! $this->canAccessBantuanGroup($user, $bantuan)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bantuan;
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
