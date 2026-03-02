<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_notulen_rapats', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->string('title');
            $table->string('person_name')->nullable();
            $table->string('institution')->nullable();
            $table->text('description')->nullable();
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index('entry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_notulen_rapats');
    }
};
