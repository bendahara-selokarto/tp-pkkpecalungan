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
        Schema::create('foto_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->date('activity_date');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            
            // Scoping fields
            $table->string('level'); // desa, kecamatan
            $table->unsignedBigInteger('area_id');
            $table->string('group'); // pokja-ii, pokja-iii, pokja-iv
            $table->unsignedBigInteger('created_by');
            $table->integer('tahun_anggaran');
            
            $table->timestamps();

            // Indexes
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'foto_kegiatans_scope_index');
            $table->index('group');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_kegiatans');
    }
};
