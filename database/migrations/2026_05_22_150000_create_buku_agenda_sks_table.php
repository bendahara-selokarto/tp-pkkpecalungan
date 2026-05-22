<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_agenda_sks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sk');
            $table->date('tanggal_sk');
            $table->string('kepada');
            $table->text('perihal');
            $table->text('tembusan')->nullable();
            
            // Standard attachment fields
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);

            // Contextual fields
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->integer('tahun_anggaran');
            $table->timestamps();

            $table->index(['level', 'area_id', 'tahun_anggaran'], 'buku_agenda_sks_context_idx');
            $table->index('tanggal_sk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_agenda_sks');
    }
};
