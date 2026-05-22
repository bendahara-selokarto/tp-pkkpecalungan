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
        Schema::table('buku_daftar_hadirs', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->date('attendance_date')->nullable()->change();
        });

        Schema::table('buku_tamus', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->date('visit_date')->nullable()->change();
            $table->string('guest_name')->nullable()->change();
            $table->string('purpose')->nullable()->change();

            $table->string('file_path')->nullable()->after('description');
            $table->string('original_name')->nullable()->after('file_path');
            $table->string('mime_type', 120)->nullable()->after('original_name');
            $table->string('extension', 20)->nullable()->after('mime_type');
            $table->unsignedBigInteger('size_bytes')->default(0)->after('extension');
        });

        Schema::table('buku_notulen_rapats', function (Blueprint $table) {
            $table->date('entry_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_notulen_rapats', function (Blueprint $table) {
            $table->date('entry_date')->nullable(false)->change();
        });

        Schema::table('buku_tamus', function (Blueprint $table) {
            $table->dropColumn(['title', 'file_path', 'original_name', 'mime_type', 'extension', 'size_bytes']);
            $table->date('visit_date')->nullable(false)->change();
            $table->string('guest_name')->nullable(false)->change();
            $table->string('purpose')->nullable(false)->change();
        });

        Schema::table('buku_daftar_hadirs', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->date('attendance_date')->nullable(false)->change();
        });
    }
};
