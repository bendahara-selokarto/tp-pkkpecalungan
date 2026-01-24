<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin.tp-pkk-pecalungan@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
            ]
        );

        /**
         * 4. Assign role ke user
         */
        if (! $superAdmin->hasRole($superAdminRole->name)) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
