<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kejar_pakets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kejar_paket');
            $table->string('jenis_kejar_paket');
            $table->unsignedInteger('jumlah_warga_belajar_l')->default(0);
            $table->unsignedInteger('jumlah_warga_belajar_p')->default(0);
            $table->unsignedInteger('jumlah_pengajar_l')->default(0);
            $table->unsignedInteger('jumlah_pengajar_p')->default(0);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kejar_pakets');
    }
};





