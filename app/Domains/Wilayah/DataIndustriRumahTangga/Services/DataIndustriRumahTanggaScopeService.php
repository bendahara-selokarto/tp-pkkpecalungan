<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Services;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DataIndustriRumahTanggaScopeService
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

    public function canView(User $user, DataIndustriRumahTangga $dataIndustriRumahTangga): bool
    {
        if (! $this->canAccessLevel($user, $dataIndustriRumahTangga->level)) {
            return false;
        }

        return (int) $dataIndustriRumahTangga->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, DataIndustriRumahTangga $dataIndustriRumahTangga): bool
    {
        return $this->canView($user, $dataIndustriRumahTangga);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(DataIndustriRumahTangga $dataIndustriRumahTangga, string $level, int $areaId): DataIndustriRumahTangga
    {
        if ($dataIndustriRumahTangga->level !== $level || (int) $dataIndustriRumahTangga->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $dataIndustriRumahTangga;
    }
}




