<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_tahunan_pkk_reports', function (Blueprint $table) {
            $table->id();
            $table->string('judul_laporan');
            $table->unsignedSmallInteger('tahun_laporan');
            $table->text('pendahuluan')->nullable();
            $table->text('keberhasilan')->nullable();
            $table->text('hambatan')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('penutup')->nullable();
            $table->string('disusun_oleh')->nullable();
            $table->string('jabatan_penanda_tangan')->nullable();
            $table->string('nama_penanda_tangan')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index('tahun_laporan');
            $table->unique(
                ['level', 'area_id', 'tahun_laporan'],
                'laporan_tahunan_pkk_reports_scope_year_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_tahunan_pkk_reports');
    }
};

