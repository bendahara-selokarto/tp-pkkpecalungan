<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulasi_penyuluhans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->string('jenis_simulasi_penyuluhan');
            $table->unsignedInteger('jumlah_kelompok')->default(0);
            $table->unsignedInteger('jumlah_sosialisasi')->default(0);
            $table->unsignedInteger('jumlah_kader_l')->default(0);
            $table->unsignedInteger('jumlah_kader_p')->default(0);
            $table->text('keterangan')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulasi_penyuluhans');
    }
};
