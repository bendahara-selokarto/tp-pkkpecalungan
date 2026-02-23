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

        $desaAreas = Area::where('level', 'desa')
            ->where('parent_id', $kecamatanArea->id)
            ->orderBy('id')
            ->get();

        $this->upsertUserWithRole(
            name: 'Sekretaris Kecamatan',
            email: 'sekretaris.kecamatan@gmail.com',
            plainPassword: 'password123',
            scope: 'kecamatan',
            areaId: $kecamatanArea->id,
            role: 'kecamatan-sekretaris',
        );

        foreach ($desaAreas as $desaArea) {
            $desaSlug = str($desaArea->name)->lower()->replace(' ', '.')->value();

            $this->upsertUserWithRole(
                name: 'Sekretaris Desa '.$desaArea->name,
                email: 'sekretaris.desa.'.$desaSlug.'@gmail.com',
                plainPassword: 'password123',
                scope: 'desa',
                areaId: $desaArea->id,
                role: 'desa-sekretaris',
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
