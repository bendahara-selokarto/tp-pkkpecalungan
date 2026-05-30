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
        Schema::table('buku_daftar_hadir_simulasis', function (Blueprint $table) {
            $table->dropColumn(['attendee_name', 'institution', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_daftar_hadir_simulasis', function (Blueprint $table) {
            $table->string('attendee_name')->after('title');
            $table->string('institution')->nullable()->after('attendee_name');
            $table->text('description')->nullable()->after('institution');
        });
    }
};
