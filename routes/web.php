<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Domains\Wilayah\Activities\Controllers\DesaActivityController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    ->middleware(['auth', 'role:desa'])
    ->group(function () {

        Route::resource('activities', DesaActivityController::class);
    });

require __DIR__.'/auth.php';
