<?php

namespace App\Http\Middleware;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Support\RoleScopeMatrix;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureScopeRole
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService
    ) {
    }

    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $user = $request->user();

        if (! $user || ! RoleScopeMatrix::userHasRoleForScope($user, $scope)) {
            abort(403, 'Anda tidak memiliki role untuk scope ini.');
        }

        if (! is_numeric($user->area_id)) {
            abort(403, 'Area pengguna belum ditentukan.');
        }

        $areaLevel = $this->userAreaContextService->resolveUserAreaLevel($user);

        if ($areaLevel !== $scope) {
            abort(403, 'Scope pengguna tidak sesuai area.');
        }

        return $next($request);
    }
}
