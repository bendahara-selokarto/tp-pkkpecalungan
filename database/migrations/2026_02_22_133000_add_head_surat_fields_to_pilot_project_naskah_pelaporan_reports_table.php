<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilot_project_naskah_pelaporan_reports', function (Blueprint $table) {
            $table->string('surat_kepada', 500)->nullable()->after('judul_laporan');
            $table->string('surat_dari', 500)->nullable()->after('surat_kepada');
            $table->string('surat_tembusan', 500)->nullable()->after('surat_dari');
            $table->date('surat_tanggal')->nullable()->after('surat_tembusan');
            $table->string('surat_nomor', 150)->nullable()->after('surat_tanggal');
            $table->string('surat_sifat', 150)->nullable()->after('surat_nomor');
            $table->string('surat_lampiran', 255)->nullable()->after('surat_sifat');
            $table->string('surat_hal', 500)->nullable()->after('surat_lampiran');
        });
    }

    public function down(): void
    {
        Schema::table('pilot_project_naskah_pelaporan_reports', function (Blueprint $table) {
            $table->dropColumn([
                'surat_kepada',
                'surat_dari',
                'surat_tembusan',
                'surat_tanggal',
                'surat_nomor',
                'surat_sifat',
                'surat_lampiran',
                'surat_hal',
            ]);
        });
    }
};
