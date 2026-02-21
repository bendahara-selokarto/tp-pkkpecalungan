<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilot_project_keluarga_sehat_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')
                ->constrained('pilot_project_keluarga_sehat_reports')
                ->cascadeOnDelete();
            $table->enum('section', ['data_dukung', 'pilot_project']);
            $table->string('cluster_code')->default('SUPPORT');
            $table->string('indicator_code');
            $table->string('indicator_label');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('semester')->default(1);
            $table->unsignedInteger('value')->default(0);
            $table->text('evaluation_note')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index(['section', 'cluster_code', 'sort_order']);
            $table->unique(
                ['report_id', 'section', 'cluster_code', 'indicator_code', 'year', 'semester'],
                'pilot_project_values_report_indicator_period_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilot_project_keluarga_sehat_values');
    }
};

