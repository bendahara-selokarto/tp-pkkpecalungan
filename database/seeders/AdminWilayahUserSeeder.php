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

        $kecamatanSlug = $this->wilayahSlug($kecamatanArea->name);

        $this->upsertUserWithRole(
            name: 'Admin Kecamatan',
            email: 'admin-kecamatan+'.$kecamatanSlug.'@gmail.com',
            plainPassword: 'password123',
            scope: 'kecamatan',
            areaId: $kecamatanArea->id,
            role: 'admin-kecamatan',
        );

        foreach ($desaAreas as $desaArea) {
            $desaSlug = $this->wilayahSlug($desaArea->name);

            $this->upsertUserWithRole(
                name: 'Admin Desa '.$desaArea->name,
                email: 'admin-desa+'.$desaSlug.'@gmail.com',
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
        $user = User::query()
            ->where('scope', $scope)
            ->where('area_id', $areaId)
            ->whereHas('roles', fn ($query) => $query->where('name', $role))
            ->first();

        if (! $user) {
            $user = User::query()->where('email', $email)->first();
        }

        if (! $user) {
            $user = new User();
        }

        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'scope' => $scope,
            'area_id' => $areaId,
        ])->save();

        $user->syncRoles([$role]);
    }

    private function wilayahSlug(string $name): string
    {
        $slug = str($name)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '-')
            ->trim('-')
            ->value();

        return $slug !== '' ? $slug : 'wilayah';
    }
}
