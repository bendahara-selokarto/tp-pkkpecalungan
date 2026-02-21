<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Domains\Wilayah\Activities\Controllers\DesaActivityController;
use App\Domains\Wilayah\Activities\Controllers\ActivityPrintController;
use App\Domains\Wilayah\Activities\Controllers\KecamatanActivityController;
use App\Domains\Wilayah\Activities\Controllers\KecamatanDesaActivityController;
use App\Domains\Wilayah\AgendaSurat\Controllers\DesaAgendaSuratController;
use App\Domains\Wilayah\AgendaSurat\Controllers\KecamatanAgendaSuratController;
use App\Domains\Wilayah\AgendaSurat\Controllers\AgendaSuratReportPrintController;
use App\Domains\Wilayah\Inventaris\Controllers\DesaInventarisController;
use App\Domains\Wilayah\Inventaris\Controllers\KecamatanInventarisController;
use App\Domains\Wilayah\Inventaris\Controllers\InventarisReportPrintController;
use App\Domains\Wilayah\Bantuan\Controllers\DesaBantuanController;
use App\Domains\Wilayah\Bantuan\Controllers\KecamatanBantuanController;
use App\Domains\Wilayah\Bantuan\Controllers\BantuanReportPrintController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\DesaAnggotaPokjaController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\KecamatanAnggotaPokjaController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\AnggotaPokjaReportPrintController;
use App\Domains\Wilayah\AnggotaTimPenggerak\Controllers\DesaAnggotaTimPenggerakController;
use App\Domains\Wilayah\AnggotaTimPenggerak\Controllers\KecamatanAnggotaTimPenggerakController;
use App\Domains\Wilayah\AnggotaTimPenggerak\Controllers\AnggotaTimPenggerakReportPrintController;
use App\Domains\Wilayah\KaderKhusus\Controllers\DesaKaderKhususController;
use App\Domains\Wilayah\KaderKhusus\Controllers\KecamatanKaderKhususController;
use App\Domains\Wilayah\KaderKhusus\Controllers\KaderKhususReportPrintController;
use App\Domains\Wilayah\PrestasiLomba\Controllers\DesaPrestasiLombaController;
use App\Domains\Wilayah\PrestasiLomba\Controllers\KecamatanPrestasiLombaController;
use App\Domains\Wilayah\PrestasiLomba\Controllers\PrestasiLombaPrintController;
use App\Domains\Wilayah\Bkl\Controllers\DesaBklController;
use App\Domains\Wilayah\Bkl\Controllers\KecamatanBklController;
use App\Domains\Wilayah\Bkl\Controllers\BklPrintController;
use App\Domains\Wilayah\Bkr\Controllers\DesaBkrController;
use App\Domains\Wilayah\Bkr\Controllers\KecamatanBkrController;
use App\Domains\Wilayah\Bkr\Controllers\BkrPrintController;
use App\Domains\Wilayah\Koperasi\Controllers\DesaKoperasiController;
use App\Domains\Wilayah\Koperasi\Controllers\KecamatanKoperasiController;
use App\Domains\Wilayah\Koperasi\Controllers\KoperasiPrintController;
use App\Domains\Wilayah\WarungPkk\Controllers\DesaWarungPkkController;
use App\Domains\Wilayah\WarungPkk\Controllers\KecamatanWarungPkkController;
use App\Domains\Wilayah\WarungPkk\Controllers\WarungPkkPrintController;
use App\Domains\Wilayah\DataWarga\Controllers\DesaDataWargaController;
use App\Domains\Wilayah\DataWarga\Controllers\KecamatanDataWargaController;
use App\Domains\Wilayah\DataWarga\Controllers\DataWargaPrintController;
use App\Domains\Wilayah\DataKegiatanWarga\Controllers\DesaDataKegiatanWargaController;
use App\Domains\Wilayah\DataKegiatanWarga\Controllers\KecamatanDataKegiatanWargaController;
use App\Domains\Wilayah\DataKegiatanWarga\Controllers\DataKegiatanWargaPrintController;
use App\Domains\Wilayah\DataKeluarga\Controllers\DesaDataKeluargaController;
use App\Domains\Wilayah\DataKeluarga\Controllers\KecamatanDataKeluargaController;
use App\Domains\Wilayah\DataKeluarga\Controllers\DataKeluargaPrintController;
use App\Domains\Wilayah\DataIndustriRumahTangga\Controllers\DesaDataIndustriRumahTanggaController;
use App\Domains\Wilayah\DataIndustriRumahTangga\Controllers\KecamatanDataIndustriRumahTanggaController;
use App\Domains\Wilayah\DataIndustriRumahTangga\Controllers\DataIndustriRumahTanggaPrintController;
use App\Domains\Wilayah\DataPelatihanKader\Controllers\DesaDataPelatihanKaderController;
use App\Domains\Wilayah\DataPelatihanKader\Controllers\KecamatanDataPelatihanKaderController;
use App\Domains\Wilayah\DataPelatihanKader\Controllers\DataPelatihanKaderPrintController;
use App\Domains\Wilayah\CatatanKeluarga\Controllers\DesaCatatanKeluargaController;
use App\Domains\Wilayah\CatatanKeluarga\Controllers\KecamatanCatatanKeluargaController;
use App\Domains\Wilayah\CatatanKeluarga\Controllers\CatatanKeluargaPrintController;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers\DesaDataPemanfaatanTanahPekaranganHatinyaPkkController;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers\KecamatanDataPemanfaatanTanahPekaranganHatinyaPkkController;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers\DataPemanfaatanTanahPekaranganHatinyaPkkPrintController;
use App\Domains\Wilayah\TamanBacaan\Controllers\DesaTamanBacaanController;
use App\Domains\Wilayah\TamanBacaan\Controllers\KecamatanTamanBacaanController;
use App\Domains\Wilayah\TamanBacaan\Controllers\TamanBacaanPrintController;
use App\Domains\Wilayah\KejarPaket\Controllers\DesaKejarPaketController;
use App\Domains\Wilayah\KejarPaket\Controllers\KecamatanKejarPaketController;
use App\Domains\Wilayah\KejarPaket\Controllers\KejarPaketPrintController;
use App\Domains\Wilayah\Posyandu\Controllers\DesaPosyanduController;
use App\Domains\Wilayah\Posyandu\Controllers\KecamatanPosyanduController;
use App\Domains\Wilayah\Posyandu\Controllers\PosyanduPrintController;
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\DesaSimulasiPenyuluhanController;
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\KecamatanSimulasiPenyuluhanController;
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\SimulasiPenyuluhanPrintController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\DesaProgramPrioritasController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\KecamatanProgramPrioritasController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\ProgramPrioritasPrintController;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Controllers\DesaPilotProjectKeluargaSehatController;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Controllers\KecamatanPilotProjectKeluargaSehatController;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Controllers\PilotProjectKeluargaSehatPrintController;


Route::get('/', function () {
    if (auth()->user()?->hasRole('super-admin')) {
        return redirect()->route('super-admin.users.index');
    }

    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:super-admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::resource('users', UserManagementController::class);
    });

Route::prefix('desa')
    ->name('desa.')
    ->middleware(['auth', 'scope.role:desa'])
    ->group(function () {

        Route::resource('activities', DesaActivityController::class);
        Route::resource('agenda-surat', DesaAgendaSuratController::class);
        Route::resource('inventaris', DesaInventarisController::class);
        Route::resource('bantuans', DesaBantuanController::class);
        Route::resource('anggota-pokja', DesaAnggotaPokjaController::class);
        Route::resource('anggota-tim-penggerak', DesaAnggotaTimPenggerakController::class);
        Route::resource('kader-khusus', DesaKaderKhususController::class);
        Route::resource('prestasi-lomba', DesaPrestasiLombaController::class);
        Route::resource('bkl', DesaBklController::class);
        Route::resource('bkr', DesaBkrController::class);
        Route::resource('koperasi', DesaKoperasiController::class);
        Route::resource('data-warga', DesaDataWargaController::class);
        Route::resource('data-kegiatan-warga', DesaDataKegiatanWargaController::class);
        Route::resource('data-keluarga', DesaDataKeluargaController::class);
        Route::resource('data-industri-rumah-tangga', DesaDataIndustriRumahTanggaController::class);
        Route::resource('data-pelatihan-kader', DesaDataPelatihanKaderController::class);
        Route::resource('data-pemanfaatan-tanah-pekarangan-hatinya-pkk', DesaDataPemanfaatanTanahPekaranganHatinyaPkkController::class)
            ->parameters(['data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => 'dataPemanfaatan']);
        Route::get('catatan-keluarga', [DesaCatatanKeluargaController::class, 'index'])->name('catatan-keluarga.index');
        Route::resource('warung-pkk', DesaWarungPkkController::class);
        Route::resource('taman-bacaan', DesaTamanBacaanController::class);
        Route::resource('kejar-paket', DesaKejarPaketController::class);
        Route::resource('posyandu', DesaPosyanduController::class);
        Route::resource('simulasi-penyuluhan', DesaSimulasiPenyuluhanController::class);
        Route::resource('program-prioritas', DesaProgramPrioritasController::class);
        Route::resource('pilot-project-keluarga-sehat', DesaPilotProjectKeluargaSehatController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printDesa'])->name('activities.print');
        Route::get('agenda-surat/report/pdf', [AgendaSuratReportPrintController::class, 'printDesaReport'])->name('agenda-surat.report');
        Route::get('agenda-surat/ekspedisi/report/pdf', [AgendaSuratReportPrintController::class, 'printDesaEkspedisiReport'])->name('agenda-surat.ekspedisi.report');
        Route::get('inventaris/report/pdf', [InventarisReportPrintController::class, 'printDesaReport'])->name('inventaris.report');
        Route::get('bantuans/report/pdf', [BantuanReportPrintController::class, 'printDesaReport'])->name('bantuans.report');
        Route::get('bantuans/keuangan/report/pdf', [BantuanReportPrintController::class, 'printDesaKeuanganReport'])->name('bantuans.keuangan.report');
        Route::get('anggota-pokja/report/pdf', [AnggotaPokjaReportPrintController::class, 'printDesaReport'])->name('anggota-pokja.report');
        Route::get('anggota-tim-penggerak/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printDesaReport'])->name('anggota-tim-penggerak.report');
        Route::get('anggota-tim-penggerak-kader/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printDesaAnggotaDanKaderReport'])->name('anggota-tim-penggerak-kader.report');
        Route::get('kader-khusus/report/pdf', [KaderKhususReportPrintController::class, 'printDesaReport'])->name('kader-khusus.report');
        Route::get('prestasi-lomba/report/pdf', [PrestasiLombaPrintController::class, 'printDesaReport'])->name('prestasi-lomba.report');
        Route::get('bkl/report/pdf', [BklPrintController::class, 'printDesaReport'])->name('bkl.report');
        Route::get('bkr/report/pdf', [BkrPrintController::class, 'printDesaReport'])->name('bkr.report');
        Route::get('koperasi/report/pdf', [KoperasiPrintController::class, 'printDesaReport'])->name('koperasi.report');
        Route::get('data-warga/report/pdf', [DataWargaPrintController::class, 'printDesaReport'])->name('data-warga.report');
        Route::get('data-kegiatan-warga/report/pdf', [DataKegiatanWargaPrintController::class, 'printDesaReport'])->name('data-kegiatan-warga.report');
        Route::get('data-keluarga/report/pdf', [DataKeluargaPrintController::class, 'printDesaReport'])->name('data-keluarga.report');
        Route::get('data-industri-rumah-tangga/report/pdf', [DataIndustriRumahTanggaPrintController::class, 'printDesaReport'])->name('data-industri-rumah-tangga.report');
        Route::get('data-pelatihan-kader/report/pdf', [DataPelatihanKaderPrintController::class, 'printDesaReport'])->name('data-pelatihan-kader.report');
        Route::get('data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf', [DataPemanfaatanTanahPekaranganHatinyaPkkPrintController::class, 'printDesaReport'])->name('data-pemanfaatan-tanah-pekarangan-hatinya-pkk.report');
        Route::get('catatan-keluarga/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaReport'])->name('catatan-keluarga.report');
        Route::get('warung-pkk/report/pdf', [WarungPkkPrintController::class, 'printDesaReport'])->name('warung-pkk.report');
        Route::get('taman-bacaan/report/pdf', [TamanBacaanPrintController::class, 'printDesaReport'])->name('taman-bacaan.report');
        Route::get('kejar-paket/report/pdf', [KejarPaketPrintController::class, 'printDesaReport'])->name('kejar-paket.report');
        Route::get('posyandu/report/pdf', [PosyanduPrintController::class, 'printDesaReport'])->name('posyandu.report');
        Route::get('simulasi-penyuluhan/report/pdf', [SimulasiPenyuluhanPrintController::class, 'printDesaReport'])->name('simulasi-penyuluhan.report');
        Route::get('program-prioritas/report/pdf', [ProgramPrioritasPrintController::class, 'printDesaReport'])->name('program-prioritas.report');
        Route::get('pilot-project-keluarga-sehat/report/pdf', [PilotProjectKeluargaSehatPrintController::class, 'printDesaReport'])->name('pilot-project-keluarga-sehat.report');
    });

Route::prefix('kecamatan')
    ->name('kecamatan.')
    ->middleware(['auth', 'scope.role:kecamatan'])
    ->group(function () {

        Route::resource('activities', KecamatanActivityController::class);
        Route::resource('agenda-surat', KecamatanAgendaSuratController::class);
        Route::resource('inventaris', KecamatanInventarisController::class);
        Route::resource('bantuans', KecamatanBantuanController::class);
        Route::resource('anggota-pokja', KecamatanAnggotaPokjaController::class);
        Route::resource('anggota-tim-penggerak', KecamatanAnggotaTimPenggerakController::class);
        Route::resource('kader-khusus', KecamatanKaderKhususController::class);
        Route::resource('prestasi-lomba', KecamatanPrestasiLombaController::class);
        Route::resource('bkl', KecamatanBklController::class);
        Route::resource('bkr', KecamatanBkrController::class);
        Route::resource('koperasi', KecamatanKoperasiController::class);
        Route::resource('data-warga', KecamatanDataWargaController::class);
        Route::resource('data-kegiatan-warga', KecamatanDataKegiatanWargaController::class);
        Route::resource('data-keluarga', KecamatanDataKeluargaController::class);
        Route::resource('data-industri-rumah-tangga', KecamatanDataIndustriRumahTanggaController::class);
        Route::resource('data-pelatihan-kader', KecamatanDataPelatihanKaderController::class);
        Route::resource('data-pemanfaatan-tanah-pekarangan-hatinya-pkk', KecamatanDataPemanfaatanTanahPekaranganHatinyaPkkController::class)
            ->parameters(['data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => 'dataPemanfaatan']);
        Route::get('catatan-keluarga', [KecamatanCatatanKeluargaController::class, 'index'])->name('catatan-keluarga.index');
        Route::resource('warung-pkk', KecamatanWarungPkkController::class);
        Route::resource('taman-bacaan', KecamatanTamanBacaanController::class);
        Route::resource('kejar-paket', KecamatanKejarPaketController::class);
        Route::resource('posyandu', KecamatanPosyanduController::class);
        Route::resource('simulasi-penyuluhan', KecamatanSimulasiPenyuluhanController::class);
        Route::resource('program-prioritas', KecamatanProgramPrioritasController::class);
        Route::resource('pilot-project-keluarga-sehat', KecamatanPilotProjectKeluargaSehatController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printKecamatan'])->name('activities.print');
        Route::get('agenda-surat/report/pdf', [AgendaSuratReportPrintController::class, 'printKecamatanReport'])->name('agenda-surat.report');
        Route::get('agenda-surat/ekspedisi/report/pdf', [AgendaSuratReportPrintController::class, 'printKecamatanEkspedisiReport'])->name('agenda-surat.ekspedisi.report');
        Route::get('inventaris/report/pdf', [InventarisReportPrintController::class, 'printKecamatanReport'])->name('inventaris.report');
        Route::get('bantuans/report/pdf', [BantuanReportPrintController::class, 'printKecamatanReport'])->name('bantuans.report');
        Route::get('bantuans/keuangan/report/pdf', [BantuanReportPrintController::class, 'printKecamatanKeuanganReport'])->name('bantuans.keuangan.report');
        Route::get('anggota-pokja/report/pdf', [AnggotaPokjaReportPrintController::class, 'printKecamatanReport'])->name('anggota-pokja.report');
        Route::get('anggota-tim-penggerak/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printKecamatanReport'])->name('anggota-tim-penggerak.report');
        Route::get('anggota-tim-penggerak-kader/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printKecamatanAnggotaDanKaderReport'])->name('anggota-tim-penggerak-kader.report');
        Route::get('kader-khusus/report/pdf', [KaderKhususReportPrintController::class, 'printKecamatanReport'])->name('kader-khusus.report');
        Route::get('prestasi-lomba/report/pdf', [PrestasiLombaPrintController::class, 'printKecamatanReport'])->name('prestasi-lomba.report');
        Route::get('bkl/report/pdf', [BklPrintController::class, 'printKecamatanReport'])->name('bkl.report');
        Route::get('bkr/report/pdf', [BkrPrintController::class, 'printKecamatanReport'])->name('bkr.report');
        Route::get('koperasi/report/pdf', [KoperasiPrintController::class, 'printKecamatanReport'])->name('koperasi.report');
        Route::get('data-warga/report/pdf', [DataWargaPrintController::class, 'printKecamatanReport'])->name('data-warga.report');
        Route::get('data-kegiatan-warga/report/pdf', [DataKegiatanWargaPrintController::class, 'printKecamatanReport'])->name('data-kegiatan-warga.report');
        Route::get('data-keluarga/report/pdf', [DataKeluargaPrintController::class, 'printKecamatanReport'])->name('data-keluarga.report');
        Route::get('data-industri-rumah-tangga/report/pdf', [DataIndustriRumahTanggaPrintController::class, 'printKecamatanReport'])->name('data-industri-rumah-tangga.report');
        Route::get('data-pelatihan-kader/report/pdf', [DataPelatihanKaderPrintController::class, 'printKecamatanReport'])->name('data-pelatihan-kader.report');
        Route::get('data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf', [DataPemanfaatanTanahPekaranganHatinyaPkkPrintController::class, 'printKecamatanReport'])->name('data-pemanfaatan-tanah-pekarangan-hatinya-pkk.report');
        Route::get('catatan-keluarga/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanReport'])->name('catatan-keluarga.report');
        Route::get('warung-pkk/report/pdf', [WarungPkkPrintController::class, 'printKecamatanReport'])->name('warung-pkk.report');
        Route::get('taman-bacaan/report/pdf', [TamanBacaanPrintController::class, 'printKecamatanReport'])->name('taman-bacaan.report');
        Route::get('kejar-paket/report/pdf', [KejarPaketPrintController::class, 'printKecamatanReport'])->name('kejar-paket.report');
        Route::get('posyandu/report/pdf', [PosyanduPrintController::class, 'printKecamatanReport'])->name('posyandu.report');
        Route::get('simulasi-penyuluhan/report/pdf', [SimulasiPenyuluhanPrintController::class, 'printKecamatanReport'])->name('simulasi-penyuluhan.report');
        Route::get('desa-activities', [KecamatanDesaActivityController::class, 'index'])->name('desa-activities.index');
        Route::get('desa-activities/{id}', [KecamatanDesaActivityController::class, 'show'])->name('desa-activities.show');
        Route::get('desa-activities/{id}/print', [ActivityPrintController::class, 'printKecamatanDesa'])->name('desa-activities.print');
        Route::get('program-prioritas/report/pdf', [ProgramPrioritasPrintController::class, 'printKecamatanReport'])->name('program-prioritas.report');
        Route::get('pilot-project-keluarga-sehat/report/pdf', [PilotProjectKeluargaSehatPrintController::class, 'printKecamatanReport'])->name('pilot-project-keluarga-sehat.report');
    });

require __DIR__.'/auth.php';
