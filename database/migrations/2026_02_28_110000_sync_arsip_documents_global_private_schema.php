<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('arsip_documents')) {
            return;
        }

        Schema::table('arsip_documents', function (Blueprint $table): void {
            if (! Schema::hasColumn('arsip_documents', 'is_global')) {
                $table->boolean('is_global')->default(false);
            }

            if (! Schema::hasColumn('arsip_documents', 'level')) {
                $table->string('level', 20)->nullable();
            }

            if (! Schema::hasColumn('arsip_documents', 'area_id')) {
                $table->unsignedBigInteger('area_id')->nullable();
            }
        });

        $hasIsPublished = Schema::hasColumn('arsip_documents', 'is_published');
        $hasIsGlobal = Schema::hasColumn('arsip_documents', 'is_global');
        if ($hasIsPublished && $hasIsGlobal) {
            DB::statement('UPDATE arsip_documents SET is_global = COALESCE(is_published, 0) WHERE is_global IS NULL OR is_global = 0');
        }

        $hasCreatedBy = Schema::hasColumn('arsip_documents', 'created_by');
        $hasAreaId = Schema::hasColumn('arsip_documents', 'area_id');
        if ($hasCreatedBy && $hasAreaId && Schema::hasColumn('users', 'area_id')) {
            DB::statement(
                'UPDATE arsip_documents
                 SET area_id = (
                     SELECT users.area_id FROM users WHERE users.id = arsip_documents.created_by
                 )
                 WHERE area_id IS NULL'
            );
        }

        if ($hasAreaId) {
            $fallbackAreaId = DB::table('areas')->orderBy('id')->value('id');
            if (is_numeric($fallbackAreaId)) {
                DB::table('arsip_documents')
                    ->whereNull('area_id')
                    ->update(['area_id' => (int) $fallbackAreaId]);
            }
        }

        $hasLevel = Schema::hasColumn('arsip_documents', 'level');
        if ($hasAreaId && $hasLevel) {
            DB::statement(
                'UPDATE arsip_documents
                 SET level = (
                     SELECT areas.level FROM areas WHERE areas.id = arsip_documents.area_id
                 )
                 WHERE level IS NULL OR level = ""'
            );

            DB::table('arsip_documents')
                ->whereNull('level')
                ->orWhere('level', '')
                ->update(['level' => 'kecamatan']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('arsip_documents')) {
            return;
        }

        Schema::table('arsip_documents', function (Blueprint $table): void {
            if (Schema::hasColumn('arsip_documents', 'is_global')) {
                $table->dropColumn('is_global');
            }

            if (Schema::hasColumn('arsip_documents', 'level')) {
                $table->dropColumn('level');
            }

            if (Schema::hasColumn('arsip_documents', 'area_id')) {
                $table->dropColumn('area_id');
            }
        });
    }
};

