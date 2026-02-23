<?php

use App\Http\Middleware\EnsureScopeRole;
use App\Http\Middleware\EnsureModuleVisibility;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'scope.role' => EnsureScopeRole::class,
            'module.visibility' => EnsureModuleVisibility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Keep default Laravel throttle response, but log context for ops/debugging.
        $exceptions->render(function (ThrottleRequestsException $exception, $request) {
            Log::warning('http.throttle', [
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'route' => optional($request->route())->getName(),
                'path' => $request->path(),
                'retry_after' => $exception->getHeaders()['Retry-After'] ?? null,
            ]);

            return null;
        });

        $exceptions->render(function (UnauthorizedException $exception, $request) {
            return response()->view('errors.403', [
                'message' => 'Anda tidak memiliki akses ke halaman ini.',
            ], 403);
        });
    })
    ->create();
