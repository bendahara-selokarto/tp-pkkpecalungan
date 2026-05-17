<?php

namespace App\Domains\Wilayah\AnggotaPokja\Services;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnggotaPokjaScopeService
{
    /**
     * @var array<string, string>
     */
    private const ROLE_TO_POKJA_MAP = [
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
    private const ROLE_SCOPED_ANGGOTA_POKJA_BYPASS_ROLES = [
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
     * Resolve pokja groups that the user can access based on their roles.
     *
     * @return list<string>
     */
    public function resolvePokjaGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_POKJA_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'anggota-pokja', $groups);
    }

    /**
     * Check if user needs pokja group filtering based on their roles.
     */
    public function requiresPokjaFilter(User $user): bool
    {
        if ($user->hasAnyRole(self::ROLE_SCOPED_ANGGOTA_POKJA_BYPASS_ROLES)) {
            return false;
        }

        return true;
    }

    public function canAccessPokjaGroup(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if (! $this->requiresPokjaFilter($user)) {
            return true;
        }

        $allowedGroups = $this->resolvePokjaGroupsForUser($user);

        return in_array((string) $anggotaPokja->pokja, $allowedGroups, true);
    }

    public function canView(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if (! $this->canAccessLevel($user, $anggotaPokja->level)) {
            return false;
        }

        return (int) $anggotaPokja->area_id === (int) $user->area_id
            && (int) $anggotaPokja->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessPokjaGroup($user, $anggotaPokja);
    }

    public function canUpdate(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->canView($user, $anggotaPokja);
    }

    public function authorizeSameLevelAreaAndBudgetYear(AnggotaPokja $anggotaPokja, string $level, int $areaId, int $tahunAnggaran): AnggotaPokja
    {
        if (
            $anggotaPokja->level !== $level
            || (int) $anggotaPokja->area_id !== $areaId
            || (int) $anggotaPokja->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $anggotaPokja;
    }

    public function authorizePokjaGroup(User $user, AnggotaPokja $anggotaPokja): AnggotaPokja
    {
        if (! $this->canAccessPokjaGroup($user, $anggotaPokja)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data pokja ini.');
        }

        return $anggotaPokja;
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }

    public function requireActiveBudgetYear(): int
    {
        return $this->activeBudgetYearContextService->requireForAuthenticatedUser();
    }
}
