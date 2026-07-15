<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_prioritas', function (Blueprint $table): void {
            // Nullable so existing records are not broken.
            // Values: kesehatan | lingkungan | perencanaan_sehat
            $table->string('kategori_program_unggulan')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('program_prioritas', function (Blueprint $table): void {
            $table->dropColumn('kategori_program_unggulan');
        });
    }
};
