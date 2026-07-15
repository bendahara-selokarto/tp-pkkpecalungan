<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota_pokjas', function (Blueprint $table): void {
            // Structured field for Pokja III bidang aggregation.
            // Values: pangan | sandang | tata_laksana_rumah_tangga
            // Nullable so existing records are not broken.
            $table->string('bidang_pokja_iii')->nullable()->after('pokja');
        });
    }

    public function down(): void
    {
        Schema::table('anggota_pokjas', function (Blueprint $table): void {
            $table->dropColumn('bidang_pokja_iii');
        });
    }
};
