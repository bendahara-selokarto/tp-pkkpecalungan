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
        $this->addBudgetYearColumn('bkls');
        $this->addBudgetYearColumn('bkrs');
        $this->addBudgetYearColumn('posyandus');
        $this->addBudgetYearColumn('data_pelatihan_kaders');

        DB::table('bkls')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('bkrs')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('posyandus')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('data_pelatihan_kaders')
            ->whereNull('tahun_anggaran')
            ->whereNotNull('tahun_penyelenggaraan')
            ->update(['tahun_anggaran' => DB::raw('tahun_penyelenggaraan')]);

        DB::table('data_pelatihan_kaders')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        $this->makeBudgetYearNotNull('bkls');
        $this->makeBudgetYearNotNull('bkrs');
        $this->makeBudgetYearNotNull('posyandus');
        $this->makeBudgetYearNotNull('data_pelatihan_kaders');
    }

    public function down(): void
    {
        $this->dropBudgetYearColumn('data_pelatihan_kaders');
        $this->dropBudgetYearColumn('posyandus');
        $this->dropBudgetYearColumn('bkrs');
        $this->dropBudgetYearColumn('bkls');
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
