<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_warga_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_warga_id')->constrained('data_wargas')->cascadeOnDelete();

            $table->unsignedInteger('nomor_urut')->default(1);
            $table->string('nomor_registrasi', 100)->nullable();
            $table->string('nomor_ktp_kk', 100)->nullable();
            $table->string('nama', 255);
            $table->string('jabatan', 120)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir', 120)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->unsignedSmallInteger('umur_tahun')->nullable();
            $table->string('status_perkawinan', 100)->nullable();
            $table->string('status_dalam_keluarga', 120)->nullable();
            $table->string('agama', 100)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('desa_kel_sejenis', 150)->nullable();
            $table->string('pendidikan', 120)->nullable();
            $table->string('pekerjaan', 120)->nullable();

            $table->boolean('akseptor_kb')->default(false);
            $table->boolean('aktif_posyandu')->default(false);
            $table->boolean('ikut_bkb')->default(false);
            $table->boolean('memiliki_tabungan')->default(false);
            $table->boolean('ikut_kelompok_belajar')->default(false);
            $table->string('jenis_kelompok_belajar', 120)->nullable();
            $table->boolean('ikut_paud')->default(false);
            $table->boolean('ikut_koperasi')->default(false);
            $table->text('keterangan')->nullable();

            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['data_warga_id', 'nomor_urut']);
            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_warga_anggotas');
    }
};
