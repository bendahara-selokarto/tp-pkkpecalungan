<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_tahunan_pkk_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')
                ->constrained('laporan_tahunan_pkk_reports')
                ->cascadeOnDelete();
            $table->enum('bidang', ['sekretariat', 'pokja-i', 'pokja-ii', 'pokja-iii', 'pokja-iv']);
            $table->date('activity_date')->nullable();
            $table->text('description');
            $table->enum('entry_source', ['manual'])->default('manual');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['report_id', 'bidang']);
            $table->index(['level', 'area_id']);
            $table->index('activity_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_tahunan_pkk_entries');
    }
};

