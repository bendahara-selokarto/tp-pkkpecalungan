<?php

namespace App\Domains\Wilayah\Inventaris\Services;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InventarisScopeService
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

    public function canView(User $user, Inventaris $inventaris): bool
    {
        if (! $this->canAccessLevel($user, $inventaris->level)) {
            return false;
        }

        return (int) $inventaris->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, Inventaris $inventaris): bool
    {
        return $this->canView($user, $inventaris);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(Inventaris $inventaris, string $level, int $areaId): Inventaris
    {
        if ($inventaris->level !== $level || (int) $inventaris->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $inventaris;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}

