<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kader_khusus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('status_perkawinan', ['kawin', 'tidak_kawin']);
            $table->text('alamat');
            $table->string('pendidikan');
            $table->string('jenis_kader_khusus');
            $table->text('keterangan')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index('jenis_kader_khusus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kader_khusus');
    }
};
