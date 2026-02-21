<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilot_project_naskah_pelaporan_reports', function (Blueprint $table) {
            $table->text('penutup')->nullable()->after('pelaksanaan_5');
        });
    }

    public function down(): void
    {
        Schema::table('pilot_project_naskah_pelaporan_reports', function (Blueprint $table) {
            $table->dropColumn('penutup');
        });
    }
};
