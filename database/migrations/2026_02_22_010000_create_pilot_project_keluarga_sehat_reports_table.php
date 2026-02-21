<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilot_project_keluarga_sehat_reports', function (Blueprint $table) {
            $table->id();
            $table->string('judul_laporan');
            $table->text('dasar_hukum')->nullable();
            $table->text('pendahuluan')->nullable();
            $table->text('maksud_tujuan')->nullable();
            $table->text('pelaksanaan')->nullable();
            $table->text('dokumentasi')->nullable();
            $table->text('penutup')->nullable();
            $table->unsignedSmallInteger('tahun_awal')->default(2021);
            $table->unsignedSmallInteger('tahun_akhir')->default(2024);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->unique(
                ['level', 'area_id', 'tahun_awal', 'tahun_akhir'],
                'pilot_project_reports_scope_period_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilot_project_keluarga_sehat_reports');
    }
};

