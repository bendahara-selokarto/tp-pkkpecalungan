<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_industri_rumah_tanggas', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_jenis_industri');
            $table->string('komoditi');
            $table->string('jumlah_komoditi', 100);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->unique(['level', 'area_id', 'kategori_jenis_industri', 'komoditi'], 'data_industri_unique_scope_komoditi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_industri_rumah_tanggas');
    }
};
