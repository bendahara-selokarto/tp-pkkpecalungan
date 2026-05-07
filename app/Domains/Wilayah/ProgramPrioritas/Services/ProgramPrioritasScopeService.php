<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Services;

use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\RoleBookGroupContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProgramPrioritasScopeService
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
    private const ROLE_SCOPED_PROGRAM_PRIORITAS_BYPASS_ROLES = [
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

    public function canView(User $user, ProgramPrioritas $programPrioritas): bool
    {
        if (! $this->canAccessLevel($user, $programPrioritas->level)) {
            return false;
        }

        return (int) $programPrioritas->area_id === (int) $user->area_id
            && (int) $programPrioritas->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user)
            && $this->canAccessProgramPrioritasGroup($user, $programPrioritas);
    }

    public function canUpdate(User $user, ProgramPrioritas $programPrioritas): bool
    {
        return $this->canView($user, $programPrioritas);
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

    public function authorizeSameLevelAreaAndBudgetYear(ProgramPrioritas $programPrioritas, string $level, int $areaId, int $tahunAnggaran): ProgramPrioritas
    {
        if (
            $programPrioritas->level !== $level
            || (int) $programPrioritas->area_id !== $areaId
            || (int) $programPrioritas->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $programPrioritas;
    }

    /**
     * @return list<string>
     */
    public function resolveProgramPrioritasGroupsForUser(User $user): array
    {
        $groups = $this->roleBookGroupContextService->resolveRoleGroups($user, self::ROLE_TO_GROUP_MAP);

        return $this->roleBookGroupContextService->resolveContextualGroups($user, 'program-prioritas', $groups);
    }

    public function requireProgramPrioritasGroupForUser(User $user): string
    {
        $groups = $this->resolveProgramPrioritasGroupsForUser($user);
        if ($groups === []) {
            throw new HttpException(403, 'Pengguna tidak memiliki jabatan buku program kerja aktif.');
        }

        return $groups[0];
    }

    public function requiresProgramPrioritasGroupFilter(User $user): bool
    {
        return ! $user->hasAnyRole(self::ROLE_SCOPED_PROGRAM_PRIORITAS_BYPASS_ROLES);
    }

    public function canAccessProgramPrioritasGroup(User $user, ProgramPrioritas $programPrioritas): bool
    {
        if (! $this->requiresProgramPrioritasGroupFilter($user)) {
            return true;
        }

        return in_array((string) $programPrioritas->group, $this->resolveProgramPrioritasGroupsForUser($user), true);
    }

    public function authorizeProgramPrioritasGroup(User $user, ProgramPrioritas $programPrioritas): ProgramPrioritas
    {
        if (! $this->canAccessProgramPrioritasGroup($user, $programPrioritas)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $programPrioritas;
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
