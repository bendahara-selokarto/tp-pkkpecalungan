<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roleMap = [
            'admin_pusat' => 'admin-pusat',
            'admin_provinsi' => 'admin-provinsi',
            'admin_kabupaten' => 'admin-kabupaten',
            'admin_kecamatan' => 'admin-kecamatan',
            'admin_desa' => 'admin-desa',
            'admin_dusun' => 'admin-dusun',
            'admin_rw' => 'admin-rw',
            'admin_rt' => 'admin-rt',
            'admin_dasawisma' => 'admin-dasawisma',
        ];

        foreach ($roleMap as $oldName => $newName) {
            DB::table('roles')
                ->where('name', $oldName)
                ->where('guard_name', 'web')
                ->update(['name' => $newName]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $roleMap = [
            'admin-pusat' => 'admin_pusat',
            'admin-provinsi' => 'admin_provinsi',
            'admin-kabupaten' => 'admin_kabupaten',
            'admin-kecamatan' => 'admin_kecamatan',
            'admin-desa' => 'admin_desa',
            'admin-dusun' => 'admin_dusun',
            'admin-rw' => 'admin_rw',
            'admin-rt' => 'admin_rt',
            'admin-dasawisma' => 'admin_dasawisma',
        ];

        foreach ($roleMap as $oldName => $newName) {
            DB::table('roles')
                ->where('name', $oldName)
                ->where('guard_name', 'web')
                ->update(['name' => $newName]);
        }
    }
};
