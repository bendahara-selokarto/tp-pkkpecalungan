<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            $table->string('asal_barang')->nullable()->after('name');
            $table->date('tanggal_penerimaan')->nullable()->after('asal_barang');
            $table->string('tempat_penyimpanan')->nullable()->after('unit');
            $table->text('keterangan')->nullable()->after('description');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->string('nama_petugas')->nullable()->after('title');
            $table->string('jabatan_petugas')->nullable()->after('nama_petugas');
            $table->string('tempat_kegiatan')->nullable()->after('activity_date');
            $table->text('uraian')->nullable()->after('description');
            $table->string('tanda_tangan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            $table->dropColumn([
                'asal_barang',
                'tanggal_penerimaan',
                'tempat_penyimpanan',
                'keterangan',
            ]);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'nama_petugas',
                'jabatan_petugas',
                'tempat_kegiatan',
                'uraian',
                'tanda_tangan',
            ]);
        });
    }
};
