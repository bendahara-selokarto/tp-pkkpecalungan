<?php

namespace App\Domains\Wilayah\KaderKhusus\Services;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KaderKhususScopeService
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
    private const ROLE_SCOPED_KADER_KHUSUS_BYPASS_ROLES = [
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

    public function canView(User $user, KaderKhusus $kaderKhusus): bool
    {
        if (! $this->canAccessLevel($user, $kaderKhusus->level)) {
            return false;
        }

        return (int) $kaderKhusus->area_id === (int) $user->area_id
            && (int) $kaderKhusus->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessKaderKhususGroup($user, $kaderKhusus);
    }

    public function canUpdate(User $user, KaderKhusus $kaderKhusus): bool
    {
        return $this->canView($user, $kaderKhusus);
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

    public function authorizeSameLevelAreaAndBudgetYear(KaderKhusus $kaderKhusus, string $level, int $areaId, int $tahunAnggaran): KaderKhusus
    {
        if (
            $kaderKhusus->level !== $level
            || (int) $kaderKhusus->area_id !== $areaId
            || (int) $kaderKhusus->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $kaderKhusus;
    }

    /**
     * @return list<string>
     */
    public function resolveKaderKhususGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_GROUP_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'kader-khusus', $groups);
    }

    public function requireKaderKhususGroupForUser(User $user): string
    {
        $groups = $this->resolveKaderKhususGroupsForUser($user);
        if ($groups === []) {
            throw new HttpException(403, 'Pengguna tidak memiliki jabatan buku kader khusus aktif.');
        }

        return $groups[0];
    }

    public function requiresKaderKhususGroupFilter(User $user): bool
    {
        return ! $user->hasAnyRole(self::ROLE_SCOPED_KADER_KHUSUS_BYPASS_ROLES);
    }

    public function canAccessKaderKhususGroup(User $user, KaderKhusus $kaderKhusus): bool
    {
        if (! $this->requiresKaderKhususGroupFilter($user)) {
            return true;
        }

        return in_array((string) $kaderKhusus->group, $this->resolveKaderKhususGroupsForUser($user), true);
    }

    public function authorizeKaderKhususGroup(User $user, KaderKhusus $kaderKhusus): KaderKhusus
    {
        if (! $this->canAccessKaderKhususGroup($user, $kaderKhusus)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $kaderKhusus;
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
