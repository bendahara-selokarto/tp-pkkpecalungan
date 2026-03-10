<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_surat_lampiran_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_surat_id')
                ->constrained('agenda_surats')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->string('value');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['agenda_surat_id', 'sequence']);
            $table->index(['level', 'area_id']);
        });

        Schema::create('agenda_surat_tembusan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_surat_id')
                ->constrained('agenda_surats')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->string('value');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['agenda_surat_id', 'sequence']);
            $table->index(['level', 'area_id']);
        });

        $now = now();
        DB::table('agenda_surats')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($now): void {
                $lampiranRows = [];
                $tembusanRows = [];

                foreach ($rows as $row) {
                    $lampiranValues = $this->splitLines($row->lampiran ?? null);
                    $tembusanValues = $this->splitLines($row->tembusan ?? null);

                    if ($lampiranValues !== []) {
                        $sequence = 1;
                        foreach ($lampiranValues as $value) {
                            $lampiranRows[] = [
                                'agenda_surat_id' => $row->id,
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

                    if ($tembusanValues !== []) {
                        $sequence = 1;
                        foreach ($tembusanValues as $value) {
                            $tembusanRows[] = [
                                'agenda_surat_id' => $row->id,
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
                }

                if ($lampiranRows !== []) {
                    DB::table('agenda_surat_lampiran_items')->insert($lampiranRows);
                }

                if ($tembusanRows !== []) {
                    DB::table('agenda_surat_tembusan_items')->insert($tembusanRows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_surat_tembusan_items');
        Schema::dropIfExists('agenda_surat_lampiran_items');
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
