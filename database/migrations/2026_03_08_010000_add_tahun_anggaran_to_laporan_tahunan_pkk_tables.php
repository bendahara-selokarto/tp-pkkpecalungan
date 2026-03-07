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
        $this->addBudgetYearColumn('laporan_tahunan_pkk_reports');
        $this->addBudgetYearColumn('laporan_tahunan_pkk_entries');

        DB::table('laporan_tahunan_pkk_reports')
            ->select(['id', 'tahun_laporan'])
            ->orderBy('id')
            ->get()
            ->each(function (object $report): void {
                DB::table('laporan_tahunan_pkk_reports')
                    ->where('id', $report->id)
                    ->whereNull('tahun_anggaran')
                    ->update([
                        'tahun_anggaran' => is_numeric($report->tahun_laporan)
                            ? (int) $report->tahun_laporan
                            : self::BASELINE_BUDGET_YEAR,
                    ]);
            });

        DB::table('laporan_tahunan_pkk_entries')
            ->leftJoin(
                'laporan_tahunan_pkk_reports',
                'laporan_tahunan_pkk_reports.id',
                '=',
                'laporan_tahunan_pkk_entries.report_id'
            )
            ->select([
                'laporan_tahunan_pkk_entries.id',
                'laporan_tahunan_pkk_reports.tahun_anggaran as report_tahun_anggaran',
            ])
            ->orderBy('laporan_tahunan_pkk_entries.id')
            ->get()
            ->each(function (object $entry): void {
                DB::table('laporan_tahunan_pkk_entries')
                    ->where('id', $entry->id)
                    ->whereNull('tahun_anggaran')
                    ->update([
                        'tahun_anggaran' => is_numeric($entry->report_tahun_anggaran)
                            ? (int) $entry->report_tahun_anggaran
                            : self::BASELINE_BUDGET_YEAR,
                    ]);
            });

        $this->makeBudgetYearNotNull('laporan_tahunan_pkk_reports');
        $this->makeBudgetYearNotNull('laporan_tahunan_pkk_entries');
        $this->upgradeReportUniqueConstraint();
    }

    public function down(): void
    {
        $this->restoreReportUniqueConstraint();
        $this->dropBudgetYearColumn('laporan_tahunan_pkk_entries');
        $this->dropBudgetYearColumn('laporan_tahunan_pkk_reports');
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

    private function upgradeReportUniqueConstraint(): void
    {
        Schema::table('laporan_tahunan_pkk_reports', function (Blueprint $table): void {
            $table->dropUnique('laporan_tahunan_pkk_reports_scope_year_unique');
            $table->unique(
                ['level', 'area_id', 'tahun_anggaran', 'tahun_laporan'],
                'laporan_tahunan_pkk_reports_scope_budget_year_unique'
            );
        });
    }

    private function restoreReportUniqueConstraint(): void
    {
        Schema::table('laporan_tahunan_pkk_reports', function (Blueprint $table): void {
            $table->dropUnique('laporan_tahunan_pkk_reports_scope_budget_year_unique');
            $table->unique(
                ['level', 'area_id', 'tahun_laporan'],
                'laporan_tahunan_pkk_reports_scope_year_unique'
            );
        });
    }
};
