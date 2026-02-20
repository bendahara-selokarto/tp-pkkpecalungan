<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Services;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DataKegiatanWargaScopeService
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

    public function canView(User $user, DataKegiatanWarga $dataKegiatanWarga): bool
    {
        if (! $this->canAccessLevel($user, $dataKegiatanWarga->level)) {
            return false;
        }

        return (int) $dataKegiatanWarga->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, DataKegiatanWarga $dataKegiatanWarga): bool
    {
        return $this->canView($user, $dataKegiatanWarga);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(DataKegiatanWarga $dataKegiatanWarga, string $level, int $areaId): DataKegiatanWarga
    {
        if ($dataKegiatanWarga->level !== $level || (int) $dataKegiatanWarga->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $dataKegiatanWarga;
    }
}
