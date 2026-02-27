<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Services;

use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProgramPrioritasScopeService
{
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

    public function canView(User $user, ProgramPrioritas $programPrioritas): bool
    {
        if (! $this->canAccessLevel($user, $programPrioritas->level)) {
            return false;
        }

        return (int) $programPrioritas->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, ProgramPrioritas $programPrioritas): bool
    {
        return $this->canView($user, $programPrioritas);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(ProgramPrioritas $programPrioritas, string $level, int $areaId): ProgramPrioritas
    {
        if ($programPrioritas->level !== $level || (int) $programPrioritas->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $programPrioritas;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}

