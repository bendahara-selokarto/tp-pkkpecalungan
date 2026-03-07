<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedSmallInteger('active_budget_year')->nullable()->after('scope');
            $table->index('active_budget_year');
        });

        DB::table('users')
            ->whereNull('active_budget_year')
            ->update(['active_budget_year' => (int) now()->format('Y')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['active_budget_year']);
            $table->dropColumn('active_budget_year');
        });
    }
};
