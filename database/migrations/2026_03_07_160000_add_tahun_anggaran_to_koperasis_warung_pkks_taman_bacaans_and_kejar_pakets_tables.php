<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const BASELINE_BUDGET_YEAR = 2026;

    public function up(): void
    {
        $this->addBudgetYearColumn('koperasis');
        $this->addBudgetYearColumn('warung_pkks');
        $this->addBudgetYearColumn('taman_bacaans');
        $this->addBudgetYearColumn('kejar_pakets');

        DB::table('koperasis')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('warung_pkks')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('taman_bacaans')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('kejar_pakets')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        $this->makeBudgetYearNotNull('koperasis');
        $this->makeBudgetYearNotNull('warung_pkks');
        $this->makeBudgetYearNotNull('taman_bacaans');
        $this->makeBudgetYearNotNull('kejar_pakets');
    }

    public function down(): void
    {
        $this->dropBudgetYearColumn('kejar_pakets');
        $this->dropBudgetYearColumn('taman_bacaans');
        $this->dropBudgetYearColumn('warung_pkks');
        $this->dropBudgetYearColumn('koperasis');
    }

    private function addBudgetYearColumn(string $table): void
    {
        Schema::table($table, function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], $table->getTable().'_level_area_tahun_index');
        });
    }

    private function makeBudgetYearNotNull(string $table): void
    {
        Schema::table($table, function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable(false)->change();
        });
    }

    private function dropBudgetYearColumn(string $table): void
    {
        Schema::table($table, function (Blueprint $table): void {
            $table->dropIndex($table->getTable().'_level_area_tahun_index');
            $table->dropColumn('tahun_anggaran');
        });
    }
};
