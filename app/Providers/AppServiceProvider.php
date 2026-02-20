<?php

namespace App\Providers;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
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
use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepository;
use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepository;
use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepository;
use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepositoryInterface;
use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\Repositories\BklRepository;
use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\Repositories\BkrRepository;
use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepository;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\ProgramPrioritasPolicy;
use App\Policies\AnggotaPokjaPolicy;
use App\Policies\BantuanPolicy;
use App\Policies\InventarisPolicy;
use App\Policies\KaderKhususPolicy;
use App\Policies\PrestasiLombaPolicy;
use App\Policies\BklPolicy;
use App\Policies\BkrPolicy;
use App\Policies\SimulasiPenyuluhanPolicy;
use App\Policies\UserPolicy;
use App\Repositories\SuperAdmin\UserManagementRepository;
use App\Repositories\SuperAdmin\UserManagementRepositoryInterface;
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
            ProgramPrioritasRepositoryInterface::class,
            ProgramPrioritasRepository::class
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

        $this->app->bind(
            KaderKhususRepositoryInterface::class,
            KaderKhususRepository::class
        );

        $this->app->bind(
            PrestasiLombaRepositoryInterface::class,
            PrestasiLombaRepository::class
        );

        $this->app->bind(
            BklRepositoryInterface::class,
            BklRepository::class
        );

        $this->app->bind(
            BkrRepositoryInterface::class,
            BkrRepository::class
        );

        $this->app->bind(
            SimulasiPenyuluhanRepositoryInterface::class,
            SimulasiPenyuluhanRepository::class
        );

        $this->app->bind(
            UserManagementRepositoryInterface::class,
            UserManagementRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(ProgramPrioritas::class, ProgramPrioritasPolicy::class);
        Gate::policy(Bantuan::class, BantuanPolicy::class);
        Gate::policy(Inventaris::class, InventarisPolicy::class);
        Gate::policy(AnggotaPokja::class, AnggotaPokjaPolicy::class);
        Gate::policy(KaderKhusus::class, KaderKhususPolicy::class);
        Gate::policy(PrestasiLomba::class, PrestasiLombaPolicy::class);
        Gate::policy(Bkl::class, BklPolicy::class);
        Gate::policy(Bkr::class, BkrPolicy::class);
        Gate::policy(SimulasiPenyuluhan::class, SimulasiPenyuluhanPolicy::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
