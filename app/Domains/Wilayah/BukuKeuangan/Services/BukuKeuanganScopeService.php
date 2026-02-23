<?php

namespace App\Domains\Wilayah\BukuKeuangan\Services;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuKeuanganScopeService
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

    public function canView(User $user, BukuKeuangan $bukuKeuangan): bool
    {
        if (! $this->canAccessLevel($user, $bukuKeuangan->level)) {
            return false;
        }

        return (int) $bukuKeuangan->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, BukuKeuangan $bukuKeuangan): bool
    {
        return $this->canView($user, $bukuKeuangan);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(BukuKeuangan $bukuKeuangan, string $level, int $areaId): BukuKeuangan
    {
        if ($bukuKeuangan->level !== $level || (int) $bukuKeuangan->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuKeuangan;
    }
}
