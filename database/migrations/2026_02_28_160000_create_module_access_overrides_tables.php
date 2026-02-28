<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_access_overrides', function (Blueprint $table): void {
            $table->id();
            $table->string('scope', 20);
            $table->string('role_name', 100);
            $table->string('module_slug', 100);
            $table->string('mode', 20);
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['scope', 'role_name', 'module_slug'], 'module_access_overrides_scope_role_module_unique');
            $table->index(['module_slug', 'scope'], 'module_access_overrides_module_scope_index');
        });

        Schema::create('module_access_override_audits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('module_access_override_id')
                ->nullable()
                ->constrained('module_access_overrides')
                ->nullOnDelete();
            $table->string('scope', 20);
            $table->string('role_name', 100);
            $table->string('module_slug', 100);
            $table->string('before_mode', 20)->nullable();
            $table->string('after_mode', 20)->nullable();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['module_slug', 'scope', 'role_name'], 'module_access_override_audits_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_access_override_audits');
        Schema::dropIfExists('module_access_overrides');
    }
};

