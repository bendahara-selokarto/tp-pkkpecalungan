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
        Schema::table('data_wargas', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'data_wargas_level_area_tahun_index');
        });

        Schema::table('data_warga_anggotas', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'data_warga_anggotas_level_area_tahun_index');
        });

        Schema::table('data_kegiatan_wargas', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'data_kegiatan_wargas_level_area_tahun_index');
        });

        DB::table('data_wargas')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('data_warga_anggotas')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        DB::table('data_kegiatan_wargas')
            ->whereNull('tahun_anggaran')
            ->update(['tahun_anggaran' => self::BASELINE_BUDGET_YEAR]);

        Schema::table('data_wargas', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable(false)->change();
        });

        Schema::table('data_warga_anggotas', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable(false)->change();
        });

        Schema::table('data_kegiatan_wargas', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('data_kegiatan_wargas', function (Blueprint $table): void {
            $table->dropIndex('data_kegiatan_wargas_level_area_tahun_index');
            $table->dropColumn('tahun_anggaran');
        });

        Schema::table('data_warga_anggotas', function (Blueprint $table): void {
            $table->dropIndex('data_warga_anggotas_level_area_tahun_index');
            $table->dropColumn('tahun_anggaran');
        });

        Schema::table('data_wargas', function (Blueprint $table): void {
            $table->dropIndex('data_wargas_level_area_tahun_index');
            $table->dropColumn('tahun_anggaran');
        });
    }
};
