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
use App\Domains\Wilayah\Bantuan\Controllers\DesaBantuanController;
use App\Domains\Wilayah\Bantuan\Controllers\KecamatanBantuanController;


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
    ->middleware(['auth', 'role:admin-desa'])
    ->group(function () {

        Route::resource('activities', DesaActivityController::class);
        Route::resource('inventaris', DesaInventarisController::class);
        Route::resource('bantuans', DesaBantuanController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printDesa'])->name('activities.print');
    });

Route::prefix('kecamatan')
    ->name('kecamatan.')
    ->middleware(['auth', 'role:admin-kecamatan'])
    ->group(function () {

        Route::resource('activities', KecamatanActivityController::class);
        Route::resource('inventaris', KecamatanInventarisController::class);
        Route::resource('bantuans', KecamatanBantuanController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printKecamatan'])->name('activities.print');
        Route::get('desa-activities', [KecamatanDesaActivityController::class, 'index'])->name('desa-activities.index');
        Route::get('desa-activities/{id}', [KecamatanDesaActivityController::class, 'show'])->name('desa-activities.show');
        Route::get('desa-activities/{id}/print', [ActivityPrintController::class, 'printKecamatanDesa'])->name('desa-activities.print');
    });

require __DIR__.'/auth.php';
