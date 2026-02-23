<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
use App\Domains\Wilayah\Dashboard\Services\DashboardDocumentCacheVersionService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class BuildDashboardDocumentCoverageUseCase
{
    public function __construct(
        private readonly DashboardDocumentCoverageRepositoryInterface $dashboardDocumentCoverageRepository,
        private readonly UserAreaContextService $userAreaContextService,
        private readonly DashboardDocumentCacheVersionService $dashboardDocumentCacheVersionService
    ) {
    }

    /**
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
     */
    public function execute(User $user, array $dashboardContext = []): array
    {
        $effectiveScope = $this->userAreaContextService->resolveEffectiveScope($user);

        if (! is_string($effectiveScope) || ! is_numeric($user->area_id)) {
            return $this->dashboardDocumentCoverageRepository->buildForUser($user);
        }

        $ttl = (int) config('dashboard.documents_cache_ttl_seconds', 60);
        $cacheVersion = $this->dashboardDocumentCacheVersionService->currentVersion();
        $cacheKey = $this->buildCacheKey($user, $effectiveScope, (int) $user->area_id, $dashboardContext, $cacheVersion);

        return Cache::remember(
            $cacheKey,
            now()->addSeconds(max(1, $ttl)),
            fn (): array => $this->dashboardDocumentCoverageRepository->buildForUser($user)
        );
    }

    /**
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
     */
    private function buildCacheKey(User $user, string $effectiveScope, int $areaId, array $dashboardContext, int $cacheVersion): string
    {
        $roleSignature = $this->resolveRoleSignature($user);
        $filterSignature = $this->resolveFilterSignature($dashboardContext);
        $blockSignature = $this->resolveBlockSignature($dashboardContext);

        return sprintf(
            'dashboard:documents:v3:%s:%d:%d:%s:%s:%s',
            $effectiveScope,
            $areaId,
            max(1, $cacheVersion),
            $roleSignature,
            $filterSignature,
            $blockSignature
        );
    }

    private function resolveRoleSignature(User $user): string
    {
        $roles = $user->getRoleNames()
            ->map(static fn ($role): string => (string) $role)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if ($roles === []) {
            return sha1('no-role');
        }

        return sha1(implode('|', $roles));
    }

    /**
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
     */
    private function resolveFilterSignature(array $dashboardContext): string
    {
        $mode = $this->normalizeContextToken($dashboardContext['mode'] ?? null, 'all');
        $level = $this->normalizeContextToken($dashboardContext['level'] ?? null, 'all');
        $subLevel = $this->normalizeContextToken($dashboardContext['sub_level'] ?? null, 'all');

        return sha1(sprintf(
            'mode:%s|level:%s|sub_level:%s',
            $mode,
            $level,
            $subLevel
        ));
    }

    /**
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
     */
    private function resolveBlockSignature(array $dashboardContext): string
    {
        $block = $this->normalizeContextToken($dashboardContext['block'] ?? null, 'documents');

        return sha1($block);
    }

    private function normalizeContextToken(mixed $value, string $fallback): string
    {
        if (! is_scalar($value)) {
            return $fallback;
        }

        $token = strtolower(trim((string) $value));

        return $token === '' ? $fallback : $token;
    }
}
