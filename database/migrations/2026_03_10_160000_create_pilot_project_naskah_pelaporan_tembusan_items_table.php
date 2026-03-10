<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilot_project_naskah_pelaporan_tembusan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')
                ->constrained('pilot_project_naskah_pelaporan_reports')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->string('value', 500);
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
                    $values = $this->splitLines($row->surat_tembusan ?? null);
                    if ($values === []) {
                        continue;
                    }

                    $sequence = 1;
                    foreach ($values as $value) {
                        $items[] = [
                            'report_id' => $row->id,
                            'sequence' => $sequence,
                            'value' => $value,
                            'level' => $row->level,
                            'area_id' => $row->area_id,
                            'created_by' => $row->created_by,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $sequence++;
                    }
                }

                if ($items !== []) {
                    DB::table('pilot_project_naskah_pelaporan_tembusan_items')->insert($items);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilot_project_naskah_pelaporan_tembusan_items');
    }

    /**
     * @return array<int, string>
     */
    private function splitLines(?string $value): array
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/\r\n|\r|\n/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), static fn (string $part): bool => $part !== ''));
    }
};
