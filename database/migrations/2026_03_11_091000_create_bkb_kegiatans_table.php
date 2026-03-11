<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bkb_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('jumlah_kelompok')->default(0);
            $table->unsignedInteger('jumlah_ibu_peserta')->default(0);
            $table->unsignedInteger('jumlah_ape_set')->default(0);
            $table->unsignedInteger('jumlah_kelompok_simulasi')->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('tahun_anggaran');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['level', 'area_id', 'tahun_anggaran'], 'bkb_kegiatans_scope_unique');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'bkb_kegiatans_scope_area_year_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bkb_kegiatans');
    }
};
