<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
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
                ->comment('Pokja/group yang membuat kegiatan (sekretaris-tpk, bendahara-tpk, pokja-i, pokja-ii, pokja-iii, pokja-iv)');

            $table->index(['group', 'level', 'area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['group', 'level', 'area_id']);
            $table->dropColumn('group');
        });
    }
};
