<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilot_project_naskah_pelaporan_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')
                ->constrained('pilot_project_naskah_pelaporan_reports')
                ->cascadeOnDelete();
            $table->enum('category', ['6a_photo', '6b_photo', '6d_document', '6e_photo']);
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index(['report_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilot_project_naskah_pelaporan_attachments');
    }
};
