<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bantuans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->enum('source', ['pusat', 'provinsi', 'kabupaten', 'pihak_ketiga', 'lainnya']);
            $table->decimal('amount', 15, 2);
            $table->date('received_date');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index(['source', 'received_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bantuans');
    }
};
