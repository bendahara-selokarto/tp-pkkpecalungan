<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Domains\Wilayah\Activities\Controllers\DesaActivityController;
use App\Domains\Wilayah\Activities\Controllers\ActivityPrintController;
use App\Domains\Wilayah\Activities\Controllers\KecamatanActivityController;
use App\Domains\Wilayah\Activities\Controllers\KecamatanDesaActivityController;
use App\Domains\Wilayah\Inventaris\Controllers\DesaInventarisController;
use App\Domains\Wilayah\Inventaris\Controllers\KecamatanInventarisController;
use App\Domains\Wilayah\Inventaris\Controllers\InventarisReportPrintController;
use App\Domains\Wilayah\Bantuan\Controllers\DesaBantuanController;
use App\Domains\Wilayah\Bantuan\Controllers\KecamatanBantuanController;
use App\Domains\Wilayah\Bantuan\Controllers\BantuanReportPrintController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\DesaAnggotaPokjaController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\KecamatanAnggotaPokjaController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\AnggotaPokjaReportPrintController;
use App\Domains\Wilayah\KaderKhusus\Controllers\DesaKaderKhususController;
use App\Domains\Wilayah\KaderKhusus\Controllers\KecamatanKaderKhususController;
use App\Domains\Wilayah\KaderKhusus\Controllers\KaderKhususReportPrintController;
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
        Route::resource('inventaris', DesaInventarisController::class);
        Route::resource('bantuans', DesaBantuanController::class);
        Route::resource('anggota-pokja', DesaAnggotaPokjaController::class);
        Route::resource('kader-khusus', DesaKaderKhususController::class);
        Route::resource('program-prioritas', DesaProgramPrioritasController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printDesa'])->name('activities.print');
        Route::get('inventaris/report/pdf', [InventarisReportPrintController::class, 'printDesaReport'])->name('inventaris.report');
        Route::get('bantuans/report/pdf', [BantuanReportPrintController::class, 'printDesaReport'])->name('bantuans.report');
        Route::get('anggota-pokja/report/pdf', [AnggotaPokjaReportPrintController::class, 'printDesaReport'])->name('anggota-pokja.report');
        Route::get('kader-khusus/report/pdf', [KaderKhususReportPrintController::class, 'printDesaReport'])->name('kader-khusus.report');
        Route::get('program-prioritas/report/pdf', [ProgramPrioritasPrintController::class, 'printDesaReport'])->name('program-prioritas.report');
    });

Route::prefix('kecamatan')
    ->name('kecamatan.')
    ->middleware(['auth', 'scope.role:kecamatan'])
    ->group(function () {

        Route::resource('activities', KecamatanActivityController::class);
        Route::resource('inventaris', KecamatanInventarisController::class);
        Route::resource('bantuans', KecamatanBantuanController::class);
        Route::resource('anggota-pokja', KecamatanAnggotaPokjaController::class);
        Route::resource('kader-khusus', KecamatanKaderKhususController::class);
        Route::resource('program-prioritas', KecamatanProgramPrioritasController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printKecamatan'])->name('activities.print');
        Route::get('inventaris/report/pdf', [InventarisReportPrintController::class, 'printKecamatanReport'])->name('inventaris.report');
        Route::get('bantuans/report/pdf', [BantuanReportPrintController::class, 'printKecamatanReport'])->name('bantuans.report');
        Route::get('anggota-pokja/report/pdf', [AnggotaPokjaReportPrintController::class, 'printKecamatanReport'])->name('anggota-pokja.report');
        Route::get('kader-khusus/report/pdf', [KaderKhususReportPrintController::class, 'printKecamatanReport'])->name('kader-khusus.report');
        Route::get('desa-activities', [KecamatanDesaActivityController::class, 'index'])->name('desa-activities.index');
        Route::get('desa-activities/{id}', [KecamatanDesaActivityController::class, 'show'])->name('desa-activities.show');
        Route::get('desa-activities/{id}/print', [ActivityPrintController::class, 'printKecamatanDesa'])->name('desa-activities.print');
        Route::get('program-prioritas/report/pdf', [ProgramPrioritasPrintController::class, 'printKecamatanReport'])->name('program-prioritas.report');
    });

require __DIR__.'/auth.php';
