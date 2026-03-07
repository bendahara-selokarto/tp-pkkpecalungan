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
        Schema::table('activities', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'activities_level_area_tahun_index');
        });

        DB::table('activities')
            ->select(['id', 'activity_date'])
            ->orderBy('id')
            ->get()
            ->each(function (object $activity): void {
                $tahunAnggaran = is_string($activity->activity_date) && $activity->activity_date !== ''
                    ? (int) date('Y', strtotime($activity->activity_date))
                    : self::BASELINE_BUDGET_YEAR;

                DB::table('activities')
                    ->where('id', $activity->id)
                    ->whereNull('tahun_anggaran')
                    ->update(['tahun_anggaran' => $tahunAnggaran]);
            });

        Schema::table('activities', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table): void {
            $table->dropIndex('activities_level_area_tahun_index');
            $table->dropColumn('tahun_anggaran');
        });
    }
};
