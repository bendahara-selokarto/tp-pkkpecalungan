<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_posyandu');
            $table->string('nama_pengelola');
            $table->string('nama_sekretaris');
            $table->string('jenis_posyandu');
            $table->unsignedInteger('jumlah_kader')->default(0);
            $table->string('jenis_kegiatan');
            $table->unsignedInteger('frekuensi_layanan')->default(0);
            $table->unsignedInteger('jumlah_pengunjung_l')->default(0);
            $table->unsignedInteger('jumlah_pengunjung_p')->default(0);
            $table->unsignedInteger('jumlah_petugas_l')->default(0);
            $table->unsignedInteger('jumlah_petugas_p')->default(0);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandus');
    }
};





