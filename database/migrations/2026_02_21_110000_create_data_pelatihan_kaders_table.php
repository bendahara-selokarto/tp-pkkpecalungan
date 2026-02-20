<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_pelatihan_kaders', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi', 100);
            $table->string('nama_lengkap_kader');
            $table->string('tanggal_masuk_tp_pkk', 100);
            $table->string('jabatan_fungsi');
            $table->unsignedInteger('nomor_urut_pelatihan');
            $table->string('judul_pelatihan');
            $table->string('jenis_kriteria_kaderisasi');
            $table->unsignedSmallInteger('tahun_penyelenggaraan');
            $table->string('institusi_penyelenggara');
            $table->enum('status_sertifikat', ['Bersertifikat', 'Tidak']);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->unique([
                'level',
                'area_id',
                'nomor_registrasi',
                'judul_pelatihan',
                'tahun_penyelenggaraan',
                'institusi_penyelenggara',
            ], 'data_pelatihan_kaders_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_pelatihan_kaders');
    }
};
