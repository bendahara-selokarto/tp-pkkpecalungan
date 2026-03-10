<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilot_project_naskah_pelaporan_pelaksanaan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')
                ->constrained('pilot_project_naskah_pelaporan_reports')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->text('description');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['report_id', 'sequence']);
            $table->index(['level', 'area_id']);
        });

        $now = now();
        DB::table('pilot_project_naskah_pelaporan_reports')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($now): void {
                $items = [];

                foreach ($rows as $row) {
                    for ($sequence = 1; $sequence <= 5; $sequence++) {
                        $key = "pelaksanaan_{$sequence}";
                        $value = trim((string) ($row->{$key} ?? ''));
                        if ($value === '') {
                            continue;
                        }

                        $items[] = [
                            'report_id' => $row->id,
                            'sequence' => $sequence,
                            'description' => $value,
                            'level' => $row->level,
                            'area_id' => $row->area_id,
                            'created_by' => $row->created_by,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($items !== []) {
                    DB::table('pilot_project_naskah_pelaporan_pelaksanaan_items')->insert($items);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilot_project_naskah_pelaporan_pelaksanaan_items');
    }
};
