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
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\DesaSimulasiPenyuluhanController;
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\KecamatanSimulasiPenyuluhanController;
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\SimulasiPenyuluhanPrintController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\DesaProgramPrioritasController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\KecamatanProgramPrioritasController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\ProgramPrioritasPrintController;


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
        Route::resource('simulasi-penyuluhan', DesaSimulasiPenyuluhanController::class);
        Route::resource('program-prioritas', DesaProgramPrioritasController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printDesa'])->name('activities.print');
        Route::get('agenda-surat/report/pdf', [AgendaSuratReportPrintController::class, 'printDesaReport'])->name('agenda-surat.report');
        Route::get('agenda-surat/ekspedisi/report/pdf', [AgendaSuratReportPrintController::class, 'printDesaEkspedisiReport'])->name('agenda-surat.ekspedisi.report');
        Route::get('inventaris/report/pdf', [InventarisReportPrintController::class, 'printDesaReport'])->name('inventaris.report');
        Route::get('bantuans/report/pdf', [BantuanReportPrintController::class, 'printDesaReport'])->name('bantuans.report');
        Route::get('anggota-pokja/report/pdf', [AnggotaPokjaReportPrintController::class, 'printDesaReport'])->name('anggota-pokja.report');
        Route::get('anggota-tim-penggerak/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printDesaReport'])->name('anggota-tim-penggerak.report');
        Route::get('anggota-tim-penggerak-kader/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printDesaAnggotaDanKaderReport'])->name('anggota-tim-penggerak-kader.report');
        Route::get('kader-khusus/report/pdf', [KaderKhususReportPrintController::class, 'printDesaReport'])->name('kader-khusus.report');
        Route::get('prestasi-lomba/report/pdf', [PrestasiLombaPrintController::class, 'printDesaReport'])->name('prestasi-lomba.report');
        Route::get('bkl/report/pdf', [BklPrintController::class, 'printDesaReport'])->name('bkl.report');
        Route::get('bkr/report/pdf', [BkrPrintController::class, 'printDesaReport'])->name('bkr.report');
        Route::get('simulasi-penyuluhan/report/pdf', [SimulasiPenyuluhanPrintController::class, 'printDesaReport'])->name('simulasi-penyuluhan.report');
        Route::get('program-prioritas/report/pdf', [ProgramPrioritasPrintController::class, 'printDesaReport'])->name('program-prioritas.report');
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
        Route::resource('simulasi-penyuluhan', KecamatanSimulasiPenyuluhanController::class);
        Route::resource('program-prioritas', KecamatanProgramPrioritasController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printKecamatan'])->name('activities.print');
        Route::get('agenda-surat/report/pdf', [AgendaSuratReportPrintController::class, 'printKecamatanReport'])->name('agenda-surat.report');
        Route::get('agenda-surat/ekspedisi/report/pdf', [AgendaSuratReportPrintController::class, 'printKecamatanEkspedisiReport'])->name('agenda-surat.ekspedisi.report');
        Route::get('inventaris/report/pdf', [InventarisReportPrintController::class, 'printKecamatanReport'])->name('inventaris.report');
        Route::get('bantuans/report/pdf', [BantuanReportPrintController::class, 'printKecamatanReport'])->name('bantuans.report');
        Route::get('anggota-pokja/report/pdf', [AnggotaPokjaReportPrintController::class, 'printKecamatanReport'])->name('anggota-pokja.report');
        Route::get('anggota-tim-penggerak/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printKecamatanReport'])->name('anggota-tim-penggerak.report');
        Route::get('anggota-tim-penggerak-kader/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printKecamatanAnggotaDanKaderReport'])->name('anggota-tim-penggerak-kader.report');
        Route::get('kader-khusus/report/pdf', [KaderKhususReportPrintController::class, 'printKecamatanReport'])->name('kader-khusus.report');
        Route::get('prestasi-lomba/report/pdf', [PrestasiLombaPrintController::class, 'printKecamatanReport'])->name('prestasi-lomba.report');
        Route::get('bkl/report/pdf', [BklPrintController::class, 'printKecamatanReport'])->name('bkl.report');
        Route::get('bkr/report/pdf', [BkrPrintController::class, 'printKecamatanReport'])->name('bkr.report');
        Route::get('simulasi-penyuluhan/report/pdf', [SimulasiPenyuluhanPrintController::class, 'printKecamatanReport'])->name('simulasi-penyuluhan.report');
        Route::get('desa-activities', [KecamatanDesaActivityController::class, 'index'])->name('desa-activities.index');
        Route::get('desa-activities/{id}', [KecamatanDesaActivityController::class, 'show'])->name('desa-activities.show');
        Route::get('desa-activities/{id}/print', [ActivityPrintController::class, 'printKecamatanDesa'])->name('desa-activities.print');
        Route::get('program-prioritas/report/pdf', [ProgramPrioritasPrintController::class, 'printKecamatanReport'])->name('program-prioritas.report');
    });

require __DIR__.'/auth.php';
