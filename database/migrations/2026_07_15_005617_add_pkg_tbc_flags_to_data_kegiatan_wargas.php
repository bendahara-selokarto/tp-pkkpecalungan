<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_kegiatan_wargas', function (Blueprint $table): void {
            // Structured boolean flags to replace keyword-based PKG/TBC detection.
            $table->boolean('is_pkg')->default(false)->after('keterangan');
            $table->boolean('is_tbc')->default(false)->after('is_pkg');
        });
    }

    public function down(): void
    {
        Schema::table('data_kegiatan_wargas', function (Blueprint $table): void {
            $table->dropColumn(['is_pkg', 'is_tbc']);
        });
    }
};
