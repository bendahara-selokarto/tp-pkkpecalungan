<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;

class CatatanKeluargaScopeService
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

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }
}

