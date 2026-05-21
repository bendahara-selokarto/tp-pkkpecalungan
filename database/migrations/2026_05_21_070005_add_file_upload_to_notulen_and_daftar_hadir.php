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
        Schema::table('buku_notulen_rapats', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('description');
            $table->string('original_name')->nullable()->after('file_path');
            $table->string('mime_type', 120)->nullable()->after('original_name');
            $table->string('extension', 20)->nullable()->after('mime_type');
            $table->unsignedBigInteger('size_bytes')->default(0)->after('extension');

            $table->string('person_name')->nullable()->change();
            $table->string('institution')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('buku_daftar_hadirs', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('description');
            $table->string('original_name')->nullable()->after('file_path');
            $table->string('mime_type', 120)->nullable()->after('original_name');
            $table->string('extension', 20)->nullable()->after('mime_type');
            $table->unsignedBigInteger('size_bytes')->default(0)->after('extension');

            $table->foreignId('activity_id')->nullable()->change();
            $table->string('attendee_name')->nullable()->change();
            $table->string('institution')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_notulen_rapats', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'original_name', 'mime_type', 'extension', 'size_bytes']);
            $table->string('person_name')->nullable(false)->change();
            $table->string('institution')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });

        Schema::table('buku_daftar_hadirs', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'original_name', 'mime_type', 'extension', 'size_bytes']);
            $table->foreignId('activity_id')->nullable(false)->change();
            $table->string('attendee_name')->nullable(false)->change();
            $table->string('institution')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
};
