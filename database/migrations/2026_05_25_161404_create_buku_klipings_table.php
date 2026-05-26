<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_klipings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('extension');
            $table->unsignedBigInteger('size_bytes');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->year('tahun_anggaran');
            $table->timestamps();

            $table->index(['level', 'area_id', 'tahun_anggaran'], 'buku_klipings_scope_area_year_index');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_klipings');
    }
};
