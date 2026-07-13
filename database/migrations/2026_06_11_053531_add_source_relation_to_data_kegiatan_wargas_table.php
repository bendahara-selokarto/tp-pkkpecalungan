<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_kegiatan_wargas', function (Blueprint $table) {
            $table->string('source_module')->nullable()->after('keterangan');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_module');
        });
    }

    public function down(): void
    {
        Schema::table('data_kegiatan_wargas', function (Blueprint $table) {
            $table->dropColumn(['source_module', 'source_id']);
        });
    }
};
