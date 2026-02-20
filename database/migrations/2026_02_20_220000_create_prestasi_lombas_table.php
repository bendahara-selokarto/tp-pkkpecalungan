<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi_lombas', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun');
            $table->string('jenis_lomba');
            $table->string('lokasi');
            $table->boolean('prestasi_kecamatan')->default(false);
            $table->boolean('prestasi_kabupaten')->default(false);
            $table->boolean('prestasi_provinsi')->default(false);
            $table->boolean('prestasi_nasional')->default(false);
            $table->text('keterangan')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_lombas');
    }
};
