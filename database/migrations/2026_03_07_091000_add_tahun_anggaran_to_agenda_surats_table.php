<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda_surats', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'agenda_surats_scope_area_year_index');
            $table->index('tahun_anggaran');
        });

        DB::table('agenda_surats')
            ->orderBy('id')
            ->get(['id', 'tanggal_surat'])
            ->each(function (object $agendaSurat): void {
                $tahunAnggaran = is_string($agendaSurat->tanggal_surat) && $agendaSurat->tanggal_surat !== ''
                    ? (int) date('Y', strtotime($agendaSurat->tanggal_surat))
                    : (int) now()->format('Y');

                DB::table('agenda_surats')
                    ->where('id', $agendaSurat->id)
                    ->update(['tahun_anggaran' => $tahunAnggaran]);
            });
    }

    public function down(): void
    {
        Schema::table('agenda_surats', function (Blueprint $table): void {
            $table->dropIndex('agenda_surats_scope_area_year_index');
            $table->dropIndex(['tahun_anggaran']);
            $table->dropColumn('tahun_anggaran');
        });
    }
};
