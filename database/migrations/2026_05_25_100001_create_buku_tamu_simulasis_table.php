<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_tamu_simulasis', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date');
            $table->string('guest_name');
            $table->string('purpose');
            $table->string('institution')->nullable();
            $table->text('description')->nullable();
            
            // File attachment fields
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('extension')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->integer('tahun_anggaran');
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index('visit_date');
            $table->index('tahun_anggaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_tamu_simulasis');
    }
};
