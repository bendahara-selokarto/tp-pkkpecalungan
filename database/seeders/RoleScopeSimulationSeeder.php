<?php

namespace Database\Seeders;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleScopeSimulationSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatanArea = Area::query()->where('level', 'kecamatan')->orderBy('id')->first();
        $desaArea = Area::query()->where('level', 'desa')->orderBy('id')->first();

        if (! $kecamatanArea || ! $desaArea) {
            return;
        }

        foreach (RoleScopeMatrix::assignableRolesForScope('desa') as $roleName) {
            $this->upsertDummyUser($roleName, 'desa', (int) $desaArea->id);
        }

        foreach (RoleScopeMatrix::assignableRolesForScope('kecamatan') as $roleName) {
            if ($roleName === 'super-admin') {
                continue;
            }

            $this->upsertDummyUser($roleName, 'kecamatan', (int) $kecamatanArea->id);
        }
    }

    private function upsertDummyUser(string $roleName, string $scope, int $areaId): void
    {
        Role::firstOrCreate(['name' => $roleName]);

        $email = sprintf('%s.%s@dummy.pkk.local', $scope, $roleName);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $this->displayName($roleName, $scope),
                'password' => Hash::make('password123'),
                'scope' => $scope,
                'area_id' => $areaId,
            ]
        );

        $user->forceFill([
            'scope' => $scope,
            'area_id' => $areaId,
        ])->save();

        $user->syncRoles([$roleName]);
    }

    private function displayName(string $roleName, string $scope): string
    {
        $baseLabel = str($roleName)
            ->replace("{$scope}-", '')
            ->replace('-', ' ')
            ->upper()
            ->value();

        return sprintf('Dummy %s %s', ucfirst($scope), $baseLabel);
    }
}
