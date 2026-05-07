<?php

namespace App\Domains\Wilayah\PrestasiLomba\Services;

use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PrestasiLombaScopeService
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
    private const ROLE_SCOPED_PRESTASI_BYPASS_ROLES = [
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

    public function canView(User $user, PrestasiLomba $prestasiLomba): bool
    {
        if (! $this->canAccessLevel($user, $prestasiLomba->level)) {
            return false;
        }

        return (int) $prestasiLomba->area_id === (int) $user->area_id
            && (int) $prestasiLomba->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessPrestasiGroup($user, $prestasiLomba);
    }

    public function canUpdate(User $user, PrestasiLomba $prestasiLomba): bool
    {
        return $this->canView($user, $prestasiLomba);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(PrestasiLomba $prestasiLomba, string $level, int $areaId, int $tahunAnggaran): PrestasiLomba
    {
        if (
            $prestasiLomba->level !== $level
            || (int) $prestasiLomba->area_id !== $areaId
            || (int) $prestasiLomba->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $prestasiLomba;
    }

    /**
     * @return list<string>
     */
    public function resolvePrestasiGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_GROUP_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'prestasi-lomba', $groups);
    }

    public function requirePrestasiGroupForUser(User $user): string
    {
        $groups = $this->resolvePrestasiGroupsForUser($user);
        if ($groups === []) {
            throw new HttpException(403, 'Pengguna tidak memiliki jabatan buku prestasi aktif.');
        }

        return $groups[0];
    }

    public function requiresPrestasiGroupFilter(User $user): bool
    {
        if ($user->hasAnyRole(self::ROLE_SCOPED_PRESTASI_BYPASS_ROLES)) {
            return false;
        }

        return true;
    }

    public function canAccessPrestasiGroup(User $user, PrestasiLomba $prestasiLomba): bool
    {
        if (! $this->requiresPrestasiGroupFilter($user)) {
            return true;
        }

        $allowedGroups = $this->resolvePrestasiGroupsForUser($user);

        return in_array((string) $prestasiLomba->group, $allowedGroups, true);
    }

    public function authorizePrestasiGroup(User $user, PrestasiLomba $prestasiLomba): PrestasiLomba
    {
        if (! $this->canAccessPrestasiGroup($user, $prestasiLomba)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $prestasiLomba;
    }

    public function requireAuthenticatedUser(): User
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            throw new HttpException(403, 'Pengguna tidak terautentikasi.');
        }

        return $user;
    }
}
