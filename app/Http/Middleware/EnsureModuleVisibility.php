<?php

namespace App\Http\Middleware;

use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleVisibility
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->hasRole('super-admin')) {
            return $next($request);
        }

        $scope = $this->userAreaContextService->resolveEffectiveScope($user);
        if (! is_string($scope)) {
            abort(403, 'Scope pengguna tidak valid.');
        }

        $moduleSlug = $this->resolveModuleSlugFromPath($request, $scope);
        if (! is_string($moduleSlug)) {
            return $next($request);
        }

        $mode = $this->roleMenuVisibilityService->resolveModuleModeForScope($user, $scope, $moduleSlug);
        if (! is_string($mode)) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        if ($this->isWriteIntent($request) && $mode !== RoleMenuVisibilityService::MODE_READ_WRITE) {
            abort(403, 'Modul ini hanya dapat dibaca.');
        }

        return $next($request);
    }

    private function resolveModuleSlugFromPath(Request $request, string $scope): ?string
    {
        $segments = $request->segments();
        if (($segments[0] ?? null) !== $scope) {
            return null;
        }

        $moduleSlug = $segments[1] ?? null;

        return is_string($moduleSlug) && $moduleSlug !== '' ? $moduleSlug : null;
    }

    private function isWriteIntent(Request $request): bool
    {
        $method = strtoupper($request->method());
        if (! in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        $segments = $request->segments();
        $last = end($segments);

        return is_string($last) && in_array($last, ['create', 'edit'], true);
    }
}

