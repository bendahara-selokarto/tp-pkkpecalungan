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
        $this->addBudgetYearColumn('program_prioritas');
        $this->addBudgetYearColumn('pilot_project_keluarga_sehat_reports');
        $this->addBudgetYearColumn('pilot_project_keluarga_sehat_values');
        $this->addBudgetYearColumn('pilot_project_naskah_pelaporan_reports');
        $this->addBudgetYearColumn('pilot_project_naskah_pelaporan_attachments');

        DB::table('program_prioritas')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('pilot_project_keluarga_sehat_reports')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('pilot_project_keluarga_sehat_values')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('pilot_project_naskah_pelaporan_reports')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('pilot_project_naskah_pelaporan_attachments')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        $this->makeBudgetYearNotNull('program_prioritas');
        $this->makeBudgetYearNotNull('pilot_project_keluarga_sehat_reports');
        $this->makeBudgetYearNotNull('pilot_project_keluarga_sehat_values');
        $this->makeBudgetYearNotNull('pilot_project_naskah_pelaporan_reports');
        $this->makeBudgetYearNotNull('pilot_project_naskah_pelaporan_attachments');
        $this->upgradePilotProjectKeluargaSehatUniqueConstraint();
    }

    public function down(): void
    {
        $this->restorePilotProjectKeluargaSehatUniqueConstraint();
        $this->dropBudgetYearColumn('pilot_project_naskah_pelaporan_attachments');
        $this->dropBudgetYearColumn('pilot_project_naskah_pelaporan_reports');
        $this->dropBudgetYearColumn('pilot_project_keluarga_sehat_values');
        $this->dropBudgetYearColumn('pilot_project_keluarga_sehat_reports');
        $this->dropBudgetYearColumn('program_prioritas');
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

    private function upgradePilotProjectKeluargaSehatUniqueConstraint(): void
    {
        Schema::table('pilot_project_keluarga_sehat_reports', function (Blueprint $table): void {
            $table->dropUnique('pilot_project_reports_scope_period_unique');
            $table->unique(
                ['level', 'area_id', 'tahun_anggaran', 'tahun_awal', 'tahun_akhir'],
                'pilot_project_reports_scope_budget_period_unique'
            );
        });
    }

    private function restorePilotProjectKeluargaSehatUniqueConstraint(): void
    {
        Schema::table('pilot_project_keluarga_sehat_reports', function (Blueprint $table): void {
            $table->dropUnique('pilot_project_reports_scope_budget_period_unique');
            $table->unique(
                ['level', 'area_id', 'tahun_awal', 'tahun_akhir'],
                'pilot_project_reports_scope_period_unique'
            );
        });
    }
};
