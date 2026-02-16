<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        $canResetPassword = Route::has('password.request');

        return Inertia::render('Auth/Login', [
            'status' => $request->session()->get('status'),
            'canResetPassword' => $canResetPassword,
            'routes' => [
                'login' => route('login'),
                'passwordRequest' => $canResetPassword ? route('password.request') : null,
            ],
            'labels' => [
                'email' => __('Email'),
                'password' => __('Password'),
                'rememberMe' => __('Remember me'),
                'forgotPassword' => __('Forgot your password?'),
                'logIn' => __('Log in'),
            ],
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
