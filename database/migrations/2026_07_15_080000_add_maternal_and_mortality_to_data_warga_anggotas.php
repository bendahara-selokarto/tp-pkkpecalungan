<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_warga_anggotas', function (Blueprint $table): void {
            // Maternal status: structured field replacing free-text parsing of keterangan
            // Values: hamil | melahirkan | nifas | normal (null = not applicable/unknown)
            $table->string('status_kehamilan', 20)->nullable()->after('keterangan');

            // Mortality: structured fields replacing keyword search in keterangan
            $table->boolean('is_meninggal')->default(false)->after('status_kehamilan');
            $table->date('tanggal_meninggal')->nullable()->after('is_meninggal');
            $table->string('sebab_meninggal', 255)->nullable()->after('tanggal_meninggal');
            // Values: ibu | bayi | balita | umum
            $table->string('golongan_kematian', 20)->nullable()->after('sebab_meninggal');
        });
    }

    public function down(): void
    {
        Schema::table('data_warga_anggotas', function (Blueprint $table): void {
            $table->dropColumn([
                'status_kehamilan',
                'is_meninggal',
                'tanggal_meninggal',
                'sebab_meninggal',
                'golongan_kematian',
            ]);
        });
    }
};
