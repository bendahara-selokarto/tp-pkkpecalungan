<?php

namespace App\Domains\Wilayah\Inventaris\Services;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InventarisScopeService
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
    private const ROLE_SCOPED_INVENTARIS_BYPASS_ROLES = [
        'super-admin',
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

    public function canView(User $user, Inventaris $inventaris): bool
    {
        if (! $this->canAccessLevel($user, $inventaris->level)) {
            return false;
        }

        return (int) $inventaris->area_id === (int) $user->area_id
            && (int) $inventaris->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessInventarisGroup($user, $inventaris);
    }

    public function canUpdate(User $user, Inventaris $inventaris): bool
    {
        return $this->canView($user, $inventaris);
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

    public function authorizeSameLevelAreaAndBudgetYear(Inventaris $inventaris, string $level, int $areaId, int $tahunAnggaran): Inventaris
    {
        if (
            $inventaris->level !== $level
            || (int) $inventaris->area_id !== $areaId
            || (int) $inventaris->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $inventaris;
    }

    /**
     * @return list<string>
     */
    public function resolveInventarisGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_GROUP_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'inventaris', $groups);
    }

    public function requireInventarisGroupForUser(User $user): string
    {
        $groups = $this->resolveInventarisGroupsForUser($user);
        if ($groups === []) {
            throw new HttpException(403, 'Pengguna tidak memiliki jabatan buku inventaris aktif.');
        }

        return $groups[0];
    }

    public function requiresInventarisGroupFilter(User $user): bool
    {
        return ! $user->hasAnyRole(self::ROLE_SCOPED_INVENTARIS_BYPASS_ROLES);
    }

    public function canAccessInventarisGroup(User $user, Inventaris $inventaris): bool
    {
        if (! $this->requiresInventarisGroupFilter($user)) {
            return true;
        }

        return in_array((string) $inventaris->group, $this->resolveInventarisGroupsForUser($user), true);
    }

    public function authorizeInventarisGroup(User $user, Inventaris $inventaris): Inventaris
    {
        if (! $this->canAccessInventarisGroup($user, $inventaris)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $inventaris;
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
