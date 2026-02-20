<?php

namespace App\Providers;

use Illuminate\Auth\Events\Lockout;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepository;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepository;
use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepositoryInterface;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepository;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepositoryInterface;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepository;
use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
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
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepository;
use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepositoryInterface;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepository;
use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepository;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepository;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\ProgramPrioritasPolicy;
use App\Policies\AnggotaPokjaPolicy;
use App\Policies\AnggotaTimPenggerakPolicy;
use App\Policies\BantuanPolicy;
use App\Policies\AgendaSuratPolicy;
use App\Policies\InventarisPolicy;
use App\Policies\KaderKhususPolicy;
use App\Policies\PrestasiLombaPolicy;
use App\Policies\BklPolicy;
use App\Policies\BkrPolicy;
use App\Policies\KoperasiPolicy;
use App\Policies\WarungPkkPolicy;
use App\Policies\TamanBacaanPolicy;
use App\Policies\SimulasiPenyuluhanPolicy;
use App\Policies\UserPolicy;
use App\Repositories\SuperAdmin\UserManagementRepository;
use App\Repositories\SuperAdmin\UserManagementRepositoryInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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
            AgendaSuratRepositoryInterface::class,
            AgendaSuratRepository::class
        );

        $this->app->bind(
            AnggotaPokjaRepositoryInterface::class,
            AnggotaPokjaRepository::class
        );

        $this->app->bind(
            AnggotaTimPenggerakRepositoryInterface::class,
            AnggotaTimPenggerakRepository::class
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
            KoperasiRepositoryInterface::class,
            KoperasiRepository::class
        );

        $this->app->bind(
            WarungPkkRepositoryInterface::class,
            WarungPkkRepository::class
        );

        $this->app->bind(
            TamanBacaanRepositoryInterface::class,
            TamanBacaanRepository::class
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
        Gate::policy(AgendaSurat::class, AgendaSuratPolicy::class);
        Gate::policy(AnggotaPokja::class, AnggotaPokjaPolicy::class);
        Gate::policy(AnggotaTimPenggerak::class, AnggotaTimPenggerakPolicy::class);
        Gate::policy(KaderKhusus::class, KaderKhususPolicy::class);
        Gate::policy(PrestasiLomba::class, PrestasiLombaPolicy::class);
        Gate::policy(Bkl::class, BklPolicy::class);
        Gate::policy(Bkr::class, BkrPolicy::class);
        Gate::policy(Koperasi::class, KoperasiPolicy::class);
        Gate::policy(WarungPkk::class, WarungPkkPolicy::class);
        Gate::policy(TamanBacaan::class, TamanBacaanPolicy::class);
        Gate::policy(SimulasiPenyuluhan::class, SimulasiPenyuluhanPolicy::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // Lightweight observability for auth throttling.
        Event::listen(Lockout::class, function (Lockout $event): void {
            Log::warning('auth.lockout', [
                'ip' => $event->request->ip(),
                'email' => (string) $event->request->input('email', ''),
                'user_agent' => (string) $event->request->userAgent(),
                'path' => $event->request->path(),
            ]);
        });
    }
}
