<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_kegiatan_wargas', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->boolean('aktivitas')->default(false);
            $table->text('keterangan')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->unique(['level', 'area_id', 'kegiatan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_kegiatan_wargas');
    }
};
