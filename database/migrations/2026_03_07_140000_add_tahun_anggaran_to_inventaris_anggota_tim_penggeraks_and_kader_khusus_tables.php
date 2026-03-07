<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_BUDGET_YEAR = 2026;

    public function up(): void
    {
        Schema::table('inventaris', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'inventaris_scope_area_year_index');
            $table->index('tahun_anggaran');
        });

        DB::table('inventaris')
            ->orderBy('id')
            ->get(['id', 'tanggal_penerimaan'])
            ->each(function (object $inventaris): void {
                $tahunAnggaran = is_string($inventaris->tanggal_penerimaan) && $inventaris->tanggal_penerimaan !== ''
                    ? (int) date('Y', strtotime($inventaris->tanggal_penerimaan))
                    : self::DEFAULT_BUDGET_YEAR;

                DB::table('inventaris')
                    ->where('id', $inventaris->id)
                    ->update(['tahun_anggaran' => $tahunAnggaran]);
            });

        Schema::table('anggota_tim_penggeraks', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'anggota_tim_penggeraks_scope_area_year_index');
            $table->index('tahun_anggaran');
        });

        DB::table('anggota_tim_penggeraks')->update([
            'tahun_anggaran' => self::DEFAULT_BUDGET_YEAR,
        ]);

        Schema::table('kader_khusus', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'kader_khusus_scope_area_year_index');
            $table->index('tahun_anggaran');
        });

        DB::table('kader_khusus')->update([
            'tahun_anggaran' => self::DEFAULT_BUDGET_YEAR,
        ]);
    }

    public function down(): void
    {
        Schema::table('kader_khusus', function (Blueprint $table): void {
            $table->dropIndex('kader_khusus_scope_area_year_index');
            $table->dropIndex(['tahun_anggaran']);
            $table->dropColumn('tahun_anggaran');
        });

        Schema::table('anggota_tim_penggeraks', function (Blueprint $table): void {
            $table->dropIndex('anggota_tim_penggeraks_scope_area_year_index');
            $table->dropIndex(['tahun_anggaran']);
            $table->dropColumn('tahun_anggaran');
        });

        Schema::table('inventaris', function (Blueprint $table): void {
            $table->dropIndex('inventaris_scope_area_year_index');
            $table->dropIndex(['tahun_anggaran']);
            $table->dropColumn('tahun_anggaran');
        });
    }
};
