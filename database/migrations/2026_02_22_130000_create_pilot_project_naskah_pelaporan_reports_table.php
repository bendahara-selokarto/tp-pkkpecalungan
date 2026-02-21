<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilot_project_naskah_pelaporan_reports', function (Blueprint $table) {
            $table->id();
            $table->string('judul_laporan');
            $table->text('dasar_pelaksanaan');
            $table->text('pendahuluan');
            $table->text('pelaksanaan_1');
            $table->text('pelaksanaan_2');
            $table->text('pelaksanaan_3');
            $table->text('pelaksanaan_4');
            $table->text('pelaksanaan_5');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilot_project_naskah_pelaporan_reports');
    }
};
