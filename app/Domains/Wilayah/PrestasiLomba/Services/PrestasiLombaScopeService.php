<?php

namespace App\Domains\Wilayah\PrestasiLomba\Services;

use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PrestasiLombaScopeService
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

    public function canView(User $user, PrestasiLomba $prestasiLomba): bool
    {
        if (! $this->canAccessLevel($user, $prestasiLomba->level)) {
            return false;
        }

        return (int) $prestasiLomba->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, PrestasiLomba $prestasiLomba): bool
    {
        return $this->canView($user, $prestasiLomba);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(PrestasiLomba $prestasiLomba, string $level, int $areaId): PrestasiLomba
    {
        if ($prestasiLomba->level !== $level || (int) $prestasiLomba->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $prestasiLomba;
    }
}
