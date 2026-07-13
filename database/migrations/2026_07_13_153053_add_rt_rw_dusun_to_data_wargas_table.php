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
        Schema::table('data_wargas', function (Blueprint $table) {
            $table->string('rt')->after('alamat')->default('');
            $table->string('rw')->after('rt')->default('');
            $table->string('dusun')->after('rw')->nullable();
            $table->string('alamat_detail')->after('dusun')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_wargas', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw', 'dusun', 'alamat_detail']);
        });
    }
};
