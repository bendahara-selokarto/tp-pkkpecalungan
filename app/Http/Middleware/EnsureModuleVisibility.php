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
        if (! $user || \App\Support\RoleScopeMatrix::userIsSuperAdmin($user)) {
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
        if (! is_string($moduleSlug) || $moduleSlug === '') {
            return null;
        }

        // Handle nested paths for Pokja Data Kegiatan and Data Umum under catatan-keluarga
        // e.g., /desa/catatan-keluarga/data-kegiatan-pkk-pokja-ii -> data-kegiatan-pkk-pokja-ii
        if ($moduleSlug === 'catatan-keluarga' && isset($segments[2]) && $segments[2] !== '') {
            // Only use segment 2 if it's likely a module sub-path, not an ID or 'create'
            if (! is_numeric($segments[2]) && ! in_array($segments[2], ['create', 'report'], true)) {
                return $segments[2];
            }
        }

        // Handle nested paths for Simulasi books
        // e.g., /desa/simulasi/buku-tamu -> buku-tamu-simulasi
        if ($moduleSlug === 'simulasi' && isset($segments[2]) && $segments[2] !== '') {
            if (! is_numeric($segments[2]) && ! in_array($segments[2], ['create', 'report'], true)) {
                return "{$segments[2]}-simulasi";
            }
        }

        return $moduleSlug;
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

