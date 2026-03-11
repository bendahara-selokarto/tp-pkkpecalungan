<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihan_kader_pokja_ii', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori_pelatihan', ['lp3', 'tpk_3_pkk', 'damas_pkk']);
            $table->unsignedInteger('jumlah_kader')->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('tahun_anggaran');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['level', 'area_id', 'tahun_anggaran', 'kategori_pelatihan'], 'pelatihan_kader_pokja_ii_scope_unique');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'pelatihan_kader_pokja_ii_scope_area_year_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihan_kader_pokja_ii');
    }
};
