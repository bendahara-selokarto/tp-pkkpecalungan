<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_daftar_hadirs', function (Blueprint $table) {
            $table->id();
            $table->date('attendance_date');
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->string('attendee_name');
            $table->string('institution')->nullable();
            $table->text('description')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index('attendance_date');
            $table->index('activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_daftar_hadirs');
    }
};
