<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taman_bacaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_taman_bacaan');
            $table->string('nama_pengelola');
            $table->string('jumlah_buku_bacaan');
            $table->string('jenis_buku');
            $table->string('kategori');
            $table->string('jumlah');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taman_bacaans');
    }
};


