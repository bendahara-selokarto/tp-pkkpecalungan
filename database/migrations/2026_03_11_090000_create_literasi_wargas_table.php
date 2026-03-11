<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('literasi_wargas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('jumlah_tiga_buta')->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('tahun_anggaran');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['level', 'area_id', 'tahun_anggaran'], 'literasi_wargas_scope_unique');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'literasi_wargas_scope_area_year_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('literasi_wargas');
    }
};
