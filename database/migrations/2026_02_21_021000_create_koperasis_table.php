<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koperasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_koperasi');
            $table->string('jenis_usaha');
            $table->boolean('berbadan_hukum')->default(false);
            $table->boolean('belum_berbadan_hukum')->default(false);
            $table->unsignedInteger('jumlah_anggota_l')->default(0);
            $table->unsignedInteger('jumlah_anggota_p')->default(0);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasis');
    }
};
