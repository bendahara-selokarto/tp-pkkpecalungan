<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pra_koperasi_up2k', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkat', ['pemula', 'madya', 'utama', 'mandiri']);
            $table->unsignedInteger('jumlah_kelompok')->default(0);
            $table->unsignedInteger('jumlah_peserta')->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('tahun_anggaran');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['level', 'area_id', 'tahun_anggaran', 'tingkat'], 'pra_koperasi_up2k_scope_unique');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'pra_koperasi_up2k_scope_area_year_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pra_koperasi_up2k');
    }
};
