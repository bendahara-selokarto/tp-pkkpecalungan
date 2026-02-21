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
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepository;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepository;
use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepository;
use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepository;
use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepositoryInterface;
use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepository;
use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepositoryInterface;
use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepository;
use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepository;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepository;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepository;
use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepositoryInterface;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Posyandu\Repositories\PosyanduRepository;
use App\Domains\Wilayah\Posyandu\Repositories\PosyanduRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepository;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepository;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepository;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
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
use App\Policies\DataWargaPolicy;
use App\Policies\DataKegiatanWargaPolicy;
use App\Policies\DataKeluargaPolicy;
use App\Policies\DataIndustriRumahTanggaPolicy;
use App\Policies\DataPelatihanKaderPolicy;
use App\Policies\CatatanKeluargaPolicy;
use App\Policies\DataPemanfaatanTanahPekaranganHatinyaPkkPolicy;
use App\Policies\TamanBacaanPolicy;
use App\Policies\KejarPaketPolicy;
use App\Policies\PosyanduPolicy;
use App\Policies\SimulasiPenyuluhanPolicy;
use App\Policies\PilotProjectKeluargaSehatPolicy;
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
            DataWargaRepositoryInterface::class,
            DataWargaRepository::class
        );

        $this->app->bind(
            DataKegiatanWargaRepositoryInterface::class,
            DataKegiatanWargaRepository::class
        );

        $this->app->bind(
            DataKeluargaRepositoryInterface::class,
            DataKeluargaRepository::class
        );

        $this->app->bind(
            DataIndustriRumahTanggaRepositoryInterface::class,
            DataIndustriRumahTanggaRepository::class
        );

        $this->app->bind(
            DataPelatihanKaderRepositoryInterface::class,
            DataPelatihanKaderRepository::class
        );

        $this->app->bind(
            CatatanKeluargaRepositoryInterface::class,
            CatatanKeluargaRepository::class
        );

        $this->app->bind(
            DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface::class,
            DataPemanfaatanTanahPekaranganHatinyaPkkRepository::class
        );

        $this->app->bind(
            TamanBacaanRepositoryInterface::class,
            TamanBacaanRepository::class
        );

        $this->app->bind(
            KejarPaketRepositoryInterface::class,
            KejarPaketRepository::class
        );

        $this->app->bind(
            PosyanduRepositoryInterface::class,
            PosyanduRepository::class
        );

        $this->app->bind(
            SimulasiPenyuluhanRepositoryInterface::class,
            SimulasiPenyuluhanRepository::class
        );

        $this->app->bind(
            PilotProjectKeluargaSehatRepositoryInterface::class,
            PilotProjectKeluargaSehatRepository::class
        );

        $this->app->bind(
            UserManagementRepositoryInterface::class,
            UserManagementRepository::class
        );

        $this->app->bind(
            DashboardDocumentCoverageRepositoryInterface::class,
            DashboardDocumentCoverageRepository::class
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
        Gate::policy(DataWarga::class, DataWargaPolicy::class);
        Gate::policy(DataKegiatanWarga::class, DataKegiatanWargaPolicy::class);
        Gate::policy(DataKeluarga::class, DataKeluargaPolicy::class);
        Gate::policy(DataIndustriRumahTangga::class, DataIndustriRumahTanggaPolicy::class);
        Gate::policy(DataPelatihanKader::class, DataPelatihanKaderPolicy::class);
        Gate::policy(CatatanKeluarga::class, CatatanKeluargaPolicy::class);
        Gate::policy(DataPemanfaatanTanahPekaranganHatinyaPkk::class, DataPemanfaatanTanahPekaranganHatinyaPkkPolicy::class);
        Gate::policy(TamanBacaan::class, TamanBacaanPolicy::class);
        Gate::policy(KejarPaket::class, KejarPaketPolicy::class);
        Gate::policy(Posyandu::class, PosyanduPolicy::class);
        Gate::policy(SimulasiPenyuluhan::class, SimulasiPenyuluhanPolicy::class);
        Gate::policy(PilotProjectKeluargaSehatReport::class, PilotProjectKeluargaSehatPolicy::class);

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
