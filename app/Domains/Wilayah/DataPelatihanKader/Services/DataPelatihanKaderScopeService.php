<?php

namespace App\Domains\Wilayah\DataPelatihanKader\Services;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DataPelatihanKaderScopeService
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

    public function canView(User $user, DataPelatihanKader $dataPelatihanKader): bool
    {
        if (! $this->canAccessLevel($user, $dataPelatihanKader->level)) {
            return false;
        }

        return (int) $dataPelatihanKader->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, DataPelatihanKader $dataPelatihanKader): bool
    {
        return $this->canView($user, $dataPelatihanKader);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(DataPelatihanKader $dataPelatihanKader, string $level, int $areaId): DataPelatihanKader
    {
        if ($dataPelatihanKader->level !== $level || (int) $dataPelatihanKader->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $dataPelatihanKader;
    }
}





