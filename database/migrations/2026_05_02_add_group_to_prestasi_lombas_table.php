<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasi_lombas', function (Blueprint $table) {
            $table->enum('group', [
                'sekretaris-tpk',
                'bendahara-tpk',
                'pokja-i',
                'pokja-ii',
                'pokja-iii',
                'pokja-iv',
            ])
                ->default('pokja-i')
                ->after('level')
                ->comment('Jabatan/group pemilik buku prestasi');

            $table->index(['group', 'level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::table('prestasi_lombas', function (Blueprint $table) {
            $table->dropIndex(['group', 'level', 'area_id']);
            $table->dropColumn('group');
        });
    }
};
