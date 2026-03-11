<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_khusus', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_tutor', ['kf', 'paud']);
            $table->unsignedInteger('jumlah_tutor')->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('tahun_anggaran');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['level', 'area_id', 'tahun_anggaran', 'jenis_tutor'], 'tutor_khusus_scope_unique');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'tutor_khusus_scope_area_year_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_khusus');
    }
};
