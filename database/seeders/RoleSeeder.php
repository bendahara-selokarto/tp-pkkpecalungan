<?php

namespace Database\Seeders;

use App\Support\RoleScopeMatrix;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RoleScopeMatrix::scopedRoles() as $roles) {
            foreach ($roles as $roleName) {
                Role::firstOrCreate(['name' => $roleName]);
            }
        }

        // The matrix already includes ROLE_SUPER_ADMIN in scopedRoles, 
        // but we ensure it's explicitly handled if needed.
        Role::firstOrCreate(['name' => RoleScopeMatrix::ROLE_SUPER_ADMIN]);
    }
}
