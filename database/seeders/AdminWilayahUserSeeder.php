<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Domains\Wilayah\Models\Area;

class AdminWilayahUserSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatanArea = Area::where('level', 'kecamatan')
            ->where('name', 'Pecalungan')
            ->first();

        if (! $kecamatanArea) {
            return;
        }

        $desaArea = Area::where('level', 'desa')
            ->where('parent_id', $kecamatanArea->id)
            ->where('name', 'Gombong')
            ->first();

        if (! $desaArea) {
            $desaArea = Area::where('level', 'desa')
                ->where('parent_id', $kecamatanArea->id)
                ->first();
        }

        $this->upsertUserWithRole(
            name: 'Admin Kecamatan',
            email: 'admin.kecamatan@example.com',
            plainPassword: 'password123',
            scope: 'kecamatan',
            areaId: $kecamatanArea->id,
            role: 'admin-kecamatan',
        );

        if ($desaArea) {
            $this->upsertUserWithRole(
                name: 'Admin Desa',
                email: 'admin.desa@example.com',
                plainPassword: 'password123',
                scope: 'desa',
                areaId: $desaArea->id,
                role: 'admin-desa',
            );
        }
    }

    private function upsertUserWithRole(
        string $name,
        string $email,
        string $plainPassword,
        string $scope,
        int $areaId,
        string $role,
    ): void {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $user->forceFill([
            'scope' => $scope,
            'area_id' => $areaId,
        ])->save();

        $user->syncRoles([$role]);
    }
}
