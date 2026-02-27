<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Services;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuNotulenRapatScopeService
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

    public function canView(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        if (! $this->canAccessLevel($user, $bukuNotulenRapat->level)) {
            return false;
        }

        return (int) $bukuNotulenRapat->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        return $this->canView($user, $bukuNotulenRapat);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(BukuNotulenRapat $bukuNotulenRapat, string $level, int $areaId): BukuNotulenRapat
    {
        if ($bukuNotulenRapat->level !== $level || (int) $bukuNotulenRapat->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuNotulenRapat;
    }
}
