<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class BuildDashboardDocumentCoverageUseCase
{
    public function __construct(
        private readonly DashboardDocumentCoverageRepositoryInterface $dashboardDocumentCoverageRepository,
        private readonly UserAreaContextService $userAreaContextService
    ) {
    }

    public function execute(User $user): array
    {
        $effectiveScope = $this->userAreaContextService->resolveEffectiveScope($user);

        if (! is_string($effectiveScope) || ! is_numeric($user->area_id)) {
            return $this->dashboardDocumentCoverageRepository->buildForUser($user);
        }

        $ttl = (int) config('dashboard.documents_cache_ttl_seconds', 60);
        $cacheKey = sprintf(
            'dashboard:documents:v1:%s:%d',
            $effectiveScope,
            (int) $user->area_id
        );

        return Cache::remember(
            $cacheKey,
            now()->addSeconds(max(1, $ttl)),
            fn (): array => $this->dashboardDocumentCoverageRepository->buildForUser($user)
        );
    }
}

