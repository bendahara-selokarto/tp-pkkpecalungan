<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warung_pkks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_warung_pkk');
            $table->string('nama_pengelola');
            $table->string('komoditi');
            $table->string('kategori');
            $table->string('volume');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warung_pkks');
    }
};
