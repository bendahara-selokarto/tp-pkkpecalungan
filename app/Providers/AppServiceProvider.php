<?php

namespace App\Providers;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Domains\Wilayah\Repositories\{
    AreaRepository,
    AreaRepositoryInterface
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
            $this->app->bind(
            AreaRepositoryInterface::class,
            AreaRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
