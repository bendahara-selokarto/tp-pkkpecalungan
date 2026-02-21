<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilot_project_keluarga_sehat_values', function (Blueprint $table): void {
            $table->text('keterangan_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pilot_project_keluarga_sehat_values', function (Blueprint $table): void {
            $table->dropColumn('keterangan_note');
        });
    }
};

