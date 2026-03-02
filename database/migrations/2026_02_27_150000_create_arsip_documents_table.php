<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arsip_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type', 120)->nullable();
            $table->string('extension', 20);
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->boolean('is_global')->default(false);
            $table->string('level', 20);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->unsignedBigInteger('download_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_global', 'updated_at']);
            $table->index(['level', 'area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_documents');
    }
};
