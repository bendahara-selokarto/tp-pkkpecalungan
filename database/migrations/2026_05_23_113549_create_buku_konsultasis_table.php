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
        Schema::create('buku_konsultasis', function (Blueprint $table) {
            $table->id();
            $table->date('activity_date');
            $table->text('description');
            $table->text('disposition')->nullable();
            
            // Scoping fields
            $table->string('level'); // desa, kecamatan
            $table->unsignedBigInteger('area_id');
            $table->string('group'); // sekretaris-tpk, pokja-iii
            $table->unsignedBigInteger('created_by');
            $table->integer('tahun_anggaran');
            
            $table->timestamps();

            // Indexes
            $table->index(['level', 'area_id', 'tahun_anggaran'], 'buku_konsultasis_scope_index');
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
        Schema::dropIfExists('buku_konsultasis');
    }
};
