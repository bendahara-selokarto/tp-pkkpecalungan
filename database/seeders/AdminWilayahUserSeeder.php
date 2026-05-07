<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\RoleScopeMatrix;
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

        foreach (RoleScopeMatrix::assignableRolesForScope('kecamatan') as $role) {
            $this->upsertRoleUserForArea(
                role: $role,
                scope: 'kecamatan',
                areaId: (int) $kecamatanArea->id,
                areaName: (string) $kecamatanArea->name,
            );
        }

        $this->upsertUserWithRole(
            name: 'Sekretaris Kecamatan',
            email: 'sekretaris.kecamatan@gmail.com',
            plainPassword: 'password123',
            scope: 'kecamatan',
            areaId: (int) $kecamatanArea->id,
            role: 'kecamatan-sekretaris',
        );

        foreach ($desaAreas as $desaArea) {
            foreach (RoleScopeMatrix::assignableRolesForScope('desa') as $role) {
                $this->upsertRoleUserForArea(
                    role: $role,
                    scope: 'desa',
                    areaId: (int) $desaArea->id,
                    areaName: (string) $desaArea->name,
                );
            }

            $desaSlug = str($desaArea->name)->lower()->replace(' ', '.')->value();
            $this->upsertUserWithRole(
                name: 'Sekretaris Desa '.$desaArea->name,
                email: 'sekretaris.desa.'.$desaSlug.'@gmail.com',
                plainPassword: 'password123',
                scope: 'desa',
                areaId: (int) $desaArea->id,
                role: 'desa-sekretaris',
            );
        }
    }

    private function upsertRoleUserForArea(string $role, string $scope, int $areaId, string $areaName): void
    {
        $roleLabel = str($role)
            ->replace('-', ' ')
            ->title()
            ->value();
        $areaSlug = str($areaName)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '.')
            ->trim('.')
            ->value();
        $roleSlug = str($role)
            ->lower()
            ->replace('-', '.')
            ->value();

        $this->upsertUserWithRole(
            name: $roleLabel.' '.$areaName,
            email: $roleSlug.'.'.($areaSlug !== '' ? $areaSlug : $areaId).'@gmail.com',
            plainPassword: 'password123',
            scope: $scope,
            areaId: $areaId,
            role: $role,
        );
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
            'active_budget_year' => (int) now()->format('Y'),
        ])->save();

        $user->syncRoles([$role]);
    }
}
