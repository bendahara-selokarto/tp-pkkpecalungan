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
        Schema::table('buku_tamu_simulasis', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'purpose', 'institution']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_tamu_simulasis', function (Blueprint $table) {
            $table->string('guest_name')->after('visit_date');
            $table->string('purpose')->after('guest_name');
            $table->string('institution')->nullable()->after('purpose');
        });
    }
};
