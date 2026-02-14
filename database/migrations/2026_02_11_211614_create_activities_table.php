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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();


            // informasi utama
            $table->string('title');
            $table->text('description')->nullable();

            // level kegiatan
            $table->enum('level', ['desa', 'kecamatan']);

            // relasi wilayah
            $table->foreignId('area_id')
                ->constrained('areas')
                ->cascadeOnDelete();

            // siapa yang membuat
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // tanggal kegiatan
            $table->date('activity_date');

            // optional: status jika nanti perlu approval
            $table->enum('status', ['draft', 'published'])
                ->default('draft');

            $table->timestamps();

            // index untuk performa laporan
            $table->index(['level', 'area_id']);
            $table->index('activity_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
