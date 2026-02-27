<?php

namespace App\Domains\Wilayah\AnggotaPokja\Services;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnggotaPokjaScopeService
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

    public function canView(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if (! $this->canAccessLevel($user, $anggotaPokja->level)) {
            return false;
        }

        return (int) $anggotaPokja->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->canView($user, $anggotaPokja);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(AnggotaPokja $anggotaPokja, string $level, int $areaId): AnggotaPokja
    {
        if ($anggotaPokja->level !== $level || (int) $anggotaPokja->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $anggotaPokja;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}

