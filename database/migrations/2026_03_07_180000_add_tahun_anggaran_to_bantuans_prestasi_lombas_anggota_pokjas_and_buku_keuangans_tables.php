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
        $this->addBudgetYearColumn('bantuans');
        $this->addBudgetYearColumn('prestasi_lombas');
        $this->addBudgetYearColumn('anggota_pokjas');
        $this->addBudgetYearColumn('buku_keuangans');

        DB::table('bantuans')
            ->select(['id', 'received_date'])
            ->orderBy('id')
            ->get()
            ->each(function (object $row): void {
                DB::table('bantuans')
                    ->where('id', $row->id)
                    ->update([
                        'tahun_anggaran' => $this->resolveBudgetYearFromDate($row->received_date),
                    ]);
            });

        DB::table('prestasi_lombas')
            ->select(['id', 'tahun'])
            ->orderBy('id')
            ->get()
            ->each(function (object $row): void {
                DB::table('prestasi_lombas')
                    ->where('id', $row->id)
                    ->update([
                        'tahun_anggaran' => is_numeric($row->tahun)
                            ? (int) $row->tahun
                            : self::BASELINE_BUDGET_YEAR,
                    ]);
            });

        DB::table('anggota_pokjas')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('buku_keuangans')
            ->select(['id', 'transaction_date'])
            ->orderBy('id')
            ->get()
            ->each(function (object $row): void {
                DB::table('buku_keuangans')
                    ->where('id', $row->id)
                    ->update([
                        'tahun_anggaran' => $this->resolveBudgetYearFromDate($row->transaction_date),
                    ]);
            });

        $this->makeBudgetYearNotNull('bantuans');
        $this->makeBudgetYearNotNull('prestasi_lombas');
        $this->makeBudgetYearNotNull('anggota_pokjas');
        $this->makeBudgetYearNotNull('buku_keuangans');
    }

    public function down(): void
    {
        $this->dropBudgetYearColumn('buku_keuangans');
        $this->dropBudgetYearColumn('anggota_pokjas');
        $this->dropBudgetYearColumn('prestasi_lombas');
        $this->dropBudgetYearColumn('bantuans');
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

    private function resolveBudgetYearFromDate(mixed $value): int
    {
        if (is_string($value) && preg_match('/^\d{4}/', $value) === 1) {
            return (int) substr($value, 0, 4);
        }

        return self::BASELINE_BUDGET_YEAR;
    }
};
