<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_pemanfaatan_tanah_pekarangan_hatinya_pkks', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_pemanfaatan_lahan');
            $table->string('komoditi');
            $table->string('jumlah_komoditi', 100);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->unique(['level', 'area_id', 'kategori_pemanfaatan_lahan', 'komoditi'], 'data_pemanfaatan_unique_scope_komoditi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_pemanfaatan_tanah_pekarangan_hatinya_pkks');
    }
};



