<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_prioritas', function (Blueprint $table) {
            $table->id();
            $table->string('program');
            $table->string('prioritas_program');
            $table->text('kegiatan');
            $table->text('sasaran_target');
            $table->boolean('jadwal_i')->default(false);
            $table->boolean('jadwal_ii')->default(false);
            $table->boolean('jadwal_iii')->default(false);
            $table->boolean('jadwal_iv')->default(false);
            $table->boolean('sumber_dana_pusat')->default(false);
            $table->boolean('sumber_dana_apbd')->default(false);
            $table->boolean('sumber_dana_swd')->default(false);
            $table->boolean('sumber_dana_bant')->default(false);
            $table->text('keterangan')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_prioritas');
    }
};
