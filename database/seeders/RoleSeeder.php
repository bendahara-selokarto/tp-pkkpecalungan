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

        // Keep explicit super-admin creation for clarity and compatibility.
        Role::firstOrCreate(['name' => 'super-admin']);
    }
}
