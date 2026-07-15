<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_warga_anggotas', function (Blueprint $table): void {
            $table->boolean('memiliki_asuransi_kesehatan')->default(false)->after('memiliki_tabungan');
        });
    }

    public function down(): void
    {
        Schema::table('data_warga_anggotas', function (Blueprint $table): void {
            $table->dropColumn('memiliki_asuransi_kesehatan');
        });
    }
};
