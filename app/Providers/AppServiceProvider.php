<?php

namespace App\Providers;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepository;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepository;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepositoryInterface;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepository;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepositoryInterface;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\AnggotaPokjaPolicy;
use App\Policies\BantuanPolicy;
use App\Policies\InventarisPolicy;
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

        $this->app->bind(
            ActivityRepositoryInterface::class,
            ActivityRepository::class
        );

        $this->app->bind(
            BantuanRepositoryInterface::class,
            BantuanRepository::class
        );

        $this->app->bind(
            InventarisRepositoryInterface::class,
            InventarisRepository::class
        );

        $this->app->bind(
            AnggotaPokjaRepositoryInterface::class,
            AnggotaPokjaRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(Bantuan::class, BantuanPolicy::class);
        Gate::policy(Inventaris::class, InventarisPolicy::class);
        Gate::policy(AnggotaPokja::class, AnggotaPokjaPolicy::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
