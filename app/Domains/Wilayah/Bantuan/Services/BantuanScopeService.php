<?php

namespace App\Domains\Wilayah\Bantuan\Services;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BantuanScopeService
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

    public function canView(User $user, Bantuan $bantuan): bool
    {
        if (! $this->canAccessLevel($user, $bantuan->level)) {
            return false;
        }

        return (int) $bantuan->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, Bantuan $bantuan): bool
    {
        return $this->canView($user, $bantuan);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(Bantuan $bantuan, string $level, int $areaId): Bantuan
    {
        if ($bantuan->level !== $level || (int) $bantuan->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bantuan;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}

