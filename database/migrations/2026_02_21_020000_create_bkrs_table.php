<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bkrs', function (Blueprint $table) {
            $table->id();
            $table->string('desa');
            $table->string('nama_bkr');
            $table->string('no_tgl_sk');
            $table->string('nama_ketua_kelompok');
            $table->unsignedInteger('jumlah_anggota')->default(0);
            $table->text('kegiatan');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bkrs');
    }
};
