<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : Inertia::render('Auth/VerifyEmail', [
                        'status' => $request->session()->get('status'),
                        'routes' => [
                            'verificationSend' => route('verification.send'),
                            'logout' => route('logout'),
                        ],
                        'labels' => [
                            'description' => __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.'),
                            'resent' => __('A new verification link has been sent to the email address you provided during registration.'),
                            'resend' => __('Resend Verification Email'),
                            'logout' => __('Log Out'),
                        ],
                    ]);
    }
}
