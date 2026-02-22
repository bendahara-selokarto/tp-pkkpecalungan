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
            $this->upsertDummyUser($roleName, 'desa', (int) $desaArea->id, (string) $desaArea->name);
        }

        foreach (RoleScopeMatrix::assignableRolesForScope('kecamatan') as $roleName) {
            if ($roleName === 'super-admin') {
                continue;
            }

            $this->upsertDummyUser($roleName, 'kecamatan', (int) $kecamatanArea->id, (string) $kecamatanArea->name);
        }
    }

    private function upsertDummyUser(string $roleName, string $scope, int $areaId, string $wilayahName): void
    {
        Role::firstOrCreate(['name' => $roleName]);

        $email = sprintf('%s+%s@gmail.com', $roleName, $this->wilayahSlug($wilayahName));

        $user = User::query()
            ->where('scope', $scope)
            ->where('area_id', $areaId)
            ->whereHas('roles', fn ($query) => $query->where('name', $roleName))
            ->first();

        if (! $user) {
            $user = User::query()->where('email', $email)->first();
        }

        if (! $user) {
            $user = new User();
        }

        $user->forceFill([
            'name' => $this->displayName($roleName, $scope),
            'email' => $email,
            'password' => Hash::make('password123'),
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
