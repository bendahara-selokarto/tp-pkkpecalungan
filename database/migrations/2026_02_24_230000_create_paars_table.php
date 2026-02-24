<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paars', function (Blueprint $table) {
            $table->id();
            $table->string('indikator', 64);
            $table->unsignedInteger('jumlah')->default(0);
            $table->string('keterangan')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->unique(['level', 'area_id', 'indikator']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paars');
    }
};