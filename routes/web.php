<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Domains\Wilayah\Activities\Controllers\DesaActivityController;
use App\Domains\Wilayah\Activities\Controllers\KecamatanActivityController;
use App\Domains\Wilayah\Activities\Controllers\KecamatanDesaActivityController;


Route::get('/', function () {
    return view('welcome');
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
    });

Route::prefix('kecamatan')
    ->name('kecamatan.')
    ->middleware(['auth', 'role:admin-kecamatan'])
    ->group(function () {

        Route::resource('activities', KecamatanActivityController::class);
        Route::get('desa-activities', [KecamatanDesaActivityController::class, 'index'])->name('desa-activities.index');
        Route::get('desa-activities/{id}', [KecamatanDesaActivityController::class, 'show'])->name('desa-activities.show');
    });

require __DIR__.'/auth.php';
