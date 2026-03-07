<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku_tamus', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'buku_tamus_scope_area_year_index');
            $table->index('tahun_anggaran');
        });

        DB::table('buku_tamus')
            ->orderBy('id')
            ->get(['id', 'visit_date'])
            ->each(function (object $bukuTamu): void {
                $tahunAnggaran = is_string($bukuTamu->visit_date) && $bukuTamu->visit_date !== ''
                    ? (int) date('Y', strtotime($bukuTamu->visit_date))
                    : (int) now()->format('Y');

                DB::table('buku_tamus')
                    ->where('id', $bukuTamu->id)
                    ->update(['tahun_anggaran' => $tahunAnggaran]);
            });

        Schema::table('buku_daftar_hadirs', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'buku_daftar_hadirs_scope_area_year_index');
            $table->index('tahun_anggaran');
        });

        DB::table('buku_daftar_hadirs')
            ->orderBy('id')
            ->get(['id', 'attendance_date'])
            ->each(function (object $bukuDaftarHadir): void {
                $tahunAnggaran = is_string($bukuDaftarHadir->attendance_date) && $bukuDaftarHadir->attendance_date !== ''
                    ? (int) date('Y', strtotime($bukuDaftarHadir->attendance_date))
                    : (int) now()->format('Y');

                DB::table('buku_daftar_hadirs')
                    ->where('id', $bukuDaftarHadir->id)
                    ->update(['tahun_anggaran' => $tahunAnggaran]);
            });

        Schema::table('buku_notulen_rapats', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun_anggaran')->nullable()->after('created_by');
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'buku_notulen_rapats_scope_area_year_index');
            $table->index('tahun_anggaran');
        });

        DB::table('buku_notulen_rapats')
            ->orderBy('id')
            ->get(['id', 'entry_date'])
            ->each(function (object $bukuNotulenRapat): void {
                $tahunAnggaran = is_string($bukuNotulenRapat->entry_date) && $bukuNotulenRapat->entry_date !== ''
                    ? (int) date('Y', strtotime($bukuNotulenRapat->entry_date))
                    : (int) now()->format('Y');

                DB::table('buku_notulen_rapats')
                    ->where('id', $bukuNotulenRapat->id)
                    ->update(['tahun_anggaran' => $tahunAnggaran]);
            });
    }

    public function down(): void
    {
        Schema::table('buku_notulen_rapats', function (Blueprint $table): void {
            $table->dropIndex('buku_notulen_rapats_scope_area_year_index');
            $table->dropIndex(['tahun_anggaran']);
            $table->dropColumn('tahun_anggaran');
        });

        Schema::table('buku_daftar_hadirs', function (Blueprint $table): void {
            $table->dropIndex('buku_daftar_hadirs_scope_area_year_index');
            $table->dropIndex(['tahun_anggaran']);
            $table->dropColumn('tahun_anggaran');
        });

        Schema::table('buku_tamus', function (Blueprint $table): void {
            $table->dropIndex('buku_tamus_scope_area_year_index');
            $table->dropIndex(['tahun_anggaran']);
            $table->dropColumn('tahun_anggaran');
        });
    }
};
