<?php

namespace Database\Seeders;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class E2ERuntimeUserSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatanArea = Area::query()->where('level', 'kecamatan')->orderBy('id')->first();
        $desaArea = Area::query()->where('level', 'desa')->orderBy('id')->first();

        if (! $kecamatanArea || ! $desaArea) {
            return;
        }

        $credentials = [
            [
                'email' => (string) env('E2E_DESA_EMAIL', 'e2e.desa@pkk.local'),
                'password' => (string) env('E2E_DESA_PASSWORD', 'password123'),
                'name' => 'E2E Desa User',
                'scope' => 'desa',
                'area_id' => (int) $desaArea->id,
                'role' => 'admin-desa',
            ],
            [
                'email' => (string) env('E2E_KECAMATAN_EMAIL', 'e2e.kecamatan@pkk.local'),
                'password' => (string) env('E2E_KECAMATAN_PASSWORD', 'password123'),
                'name' => 'E2E Kecamatan User',
                'scope' => 'kecamatan',
                'area_id' => (int) $kecamatanArea->id,
                'role' => 'admin-kecamatan',
            ],
            [
                'email' => (string) env('E2E_SUPERADMIN_EMAIL', 'e2e.superadmin@pkk.local'),
                'password' => (string) env('E2E_SUPERADMIN_PASSWORD', 'password123'),
                'name' => 'E2E Super Admin',
                'scope' => 'kecamatan',
                'area_id' => (int) $kecamatanArea->id,
                'role' => 'super-admin',
            ],
        ];

        foreach ($credentials as $account) {
            Role::firstOrCreate(['name' => $account['role']]);

            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'scope' => $account['scope'],
                    'area_id' => $account['area_id'],
                    'email_verified_at' => Carbon::now(),
                ]
            );

            $user->forceFill([
                'scope' => $account['scope'],
                'area_id' => $account['area_id'],
                'email_verified_at' => Carbon::now(),
            ])->save();

            $user->syncRoles([$account['role']]);
        }
    }
}
