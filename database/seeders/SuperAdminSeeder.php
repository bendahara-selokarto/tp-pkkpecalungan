<?php

namespace Database\Seeders;

use App\Models\User;
use App\Domains\Wilayah\Models\Area;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission (WAJIB)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * 1. Buat permissions (opsional tapi best practice)
         */
        $permissions = [
            'manage users',
            'manage roles',
            'manage permissions',
            'view dashboard',
            'full access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * 2. Buat role super-admin
         */
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Berikan semua permission ke super-admin
        $superAdminRole->syncPermissions(Permission::all());

        /**
         * 3. Buat user super admin
         */
        $targetEmail = 'super-admin+pecalungan@gmail.com';
        $superAdmin = User::query()
            ->where('email', $targetEmail)
            ->first();

        if (! $superAdmin) {
            $superAdmin = User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', $superAdminRole->name))
                ->first();
        }

        if (! $superAdmin) {
            $superAdmin = new User();
        }

        $superAdmin->forceFill([
            'name' => 'Super Admin',
            'email' => $targetEmail,
            'password' => $superAdmin->exists ? $superAdmin->password : Hash::make('password123'),
            'email_verified_at' => $superAdmin->email_verified_at ?? now(),
            'remember_token' => $superAdmin->remember_token ?? Str::random(10),
        ])->save();

        // Scope tetap valid (enum) dan area diset ke level kecamatan default.
        $defaultKecamatanId = Area::where('level', 'kecamatan')->value('id');
        if ($defaultKecamatanId !== null) {
            $superAdmin->forceFill([
                'scope' => 'kecamatan',
                'area_id' => $defaultKecamatanId,
            ])->save();
        }

        /**
         * 4. Assign role ke user
         */
        $superAdmin->syncRoles([$superAdminRole->name]);
    }
}
