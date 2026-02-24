<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_prioritas', function (Blueprint $table) {
            for ($month = 1; $month <= 12; $month++) {
                $table->boolean("jadwal_bulan_{$month}")->default(false)->after('sasaran_target');
            }
        });

        DB::table('program_prioritas')->update([
            'jadwal_bulan_1' => DB::raw('jadwal_i'),
            'jadwal_bulan_2' => DB::raw('jadwal_i'),
            'jadwal_bulan_3' => DB::raw('jadwal_i'),
            'jadwal_bulan_4' => DB::raw('jadwal_ii'),
            'jadwal_bulan_5' => DB::raw('jadwal_ii'),
            'jadwal_bulan_6' => DB::raw('jadwal_ii'),
            'jadwal_bulan_7' => DB::raw('jadwal_iii'),
            'jadwal_bulan_8' => DB::raw('jadwal_iii'),
            'jadwal_bulan_9' => DB::raw('jadwal_iii'),
            'jadwal_bulan_10' => DB::raw('jadwal_iv'),
            'jadwal_bulan_11' => DB::raw('jadwal_iv'),
            'jadwal_bulan_12' => DB::raw('jadwal_iv'),
        ]);
    }

    public function down(): void
    {
        Schema::table('program_prioritas', function (Blueprint $table) {
            for ($month = 1; $month <= 12; $month++) {
                $table->dropColumn("jadwal_bulan_{$month}");
            }
        });
    }
};
