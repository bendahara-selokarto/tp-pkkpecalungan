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
        $this->addBudgetYearColumn('data_industri_rumah_tanggas');
        $this->addBudgetYearColumn('data_pemanfaatan_tanah_pekarangan_hatinya_pkks');
        $this->addBudgetYearColumn('paars');
        $this->addBudgetYearColumn('simulasi_penyuluhans');

        DB::table('data_industri_rumah_tanggas')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('data_pemanfaatan_tanah_pekarangan_hatinya_pkks')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('paars')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('simulasi_penyuluhans')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        $this->makeBudgetYearNotNull('data_industri_rumah_tanggas');
        $this->makeBudgetYearNotNull('data_pemanfaatan_tanah_pekarangan_hatinya_pkks');
        $this->makeBudgetYearNotNull('paars');
        $this->makeBudgetYearNotNull('simulasi_penyuluhans');
        $this->upgradePaarUniqueConstraint();
    }

    public function down(): void
    {
        $this->restorePaarUniqueConstraint();
        $this->dropBudgetYearColumn('simulasi_penyuluhans');
        $this->dropBudgetYearColumn('paars');
        $this->dropBudgetYearColumn('data_pemanfaatan_tanah_pekarangan_hatinya_pkks');
        $this->dropBudgetYearColumn('data_industri_rumah_tanggas');
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

    private function upgradePaarUniqueConstraint(): void
    {
        Schema::table('paars', function (Blueprint $table): void {
            $table->dropUnique('paars_level_area_id_indikator_unique');
            $table->unique(['level', 'area_id', 'tahun_anggaran', 'indikator'], 'paars_level_area_tahun_indikator_unique');
        });
    }

    private function restorePaarUniqueConstraint(): void
    {
        Schema::table('paars', function (Blueprint $table): void {
            $table->dropUnique('paars_level_area_tahun_indikator_unique');
            $table->unique(['level', 'area_id', 'indikator']);
        });
    }
};
