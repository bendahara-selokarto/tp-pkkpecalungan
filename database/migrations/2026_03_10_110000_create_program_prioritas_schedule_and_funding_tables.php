<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_prioritas_jadwal_months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_prioritas_id')->constrained('program_prioritas')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['program_prioritas_id', 'month']);
            $table->index(['level', 'area_id']);
        });

        Schema::create('program_prioritas_funding_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_prioritas_id')->constrained('program_prioritas')->cascadeOnDelete();
            $table->string('source', 20);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['program_prioritas_id', 'source']);
            $table->index(['level', 'area_id']);
        });

        $now = now();
        DB::table('program_prioritas')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($now): void {
                $jadwalRows = [];
                $sourceRows = [];

                foreach ($rows as $row) {
                    $monthlyFlags = $this->resolveMonthlyFlags($row);
                    foreach ($monthlyFlags as $month => $flag) {
                        if (! $flag) {
                            continue;
                        }

                        $jadwalRows[] = [
                            'program_prioritas_id' => $row->id,
                            'month' => $month,
                            'level' => $row->level,
                            'area_id' => $row->area_id,
                            'created_by' => $row->created_by,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    foreach ($this->resolveFundingSources($row) as $source) {
                        $sourceRows[] = [
                            'program_prioritas_id' => $row->id,
                            'source' => $source,
                            'level' => $row->level,
                            'area_id' => $row->area_id,
                            'created_by' => $row->created_by,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($jadwalRows !== []) {
                    DB::table('program_prioritas_jadwal_months')->insert($jadwalRows);
                }

                if ($sourceRows !== []) {
                    DB::table('program_prioritas_funding_sources')->insert($sourceRows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_prioritas_funding_sources');
        Schema::dropIfExists('program_prioritas_jadwal_months');
    }

    /**
     * @return array<int, bool>
     */
    private function resolveMonthlyFlags(object $row): array
    {
        $flags = [];
        $hasMonthly = false;

        for ($month = 1; $month <= 12; $month++) {
            $key = "jadwal_bulan_{$month}";
            $flags[$month] = (bool) ($row->{$key} ?? false);
            if ($flags[$month]) {
                $hasMonthly = true;
            }
        }

        if ($hasMonthly) {
            return $flags;
        }

        $quarterFlags = [
            1 => (bool) ($row->jadwal_i ?? false),
            2 => (bool) ($row->jadwal_ii ?? false),
            3 => (bool) ($row->jadwal_iii ?? false),
            4 => (bool) ($row->jadwal_iv ?? false),
        ];

        if (! $this->hasAnyTruthyValue($quarterFlags)) {
            return $flags;
        }

        $quarterMap = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        foreach ($quarterFlags as $quarter => $active) {
            if (! $active) {
                continue;
            }

            foreach ($quarterMap[$quarter] as $month) {
                $flags[$month] = true;
            }
        }

        return $flags;
    }

    /**
     * @return array<int, string>
     */
    private function resolveFundingSources(object $row): array
    {
        $sources = [];

        if ((bool) ($row->sumber_dana_pusat ?? false)) {
            $sources[] = 'pusat';
        }

        if ((bool) ($row->sumber_dana_apbd ?? false)) {
            $sources[] = 'apbd';
        }

        if ((bool) ($row->sumber_dana_swd ?? false)) {
            $sources[] = 'swd';
        }

        if ((bool) ($row->sumber_dana_bant ?? false)) {
            $sources[] = 'bant';
        }

        return $sources;
    }

    /**
     * @param  array<int, bool>  $flags
     */
    private function hasAnyTruthyValue(array $flags): bool
    {
        foreach ($flags as $flag) {
            if ($flag) {
                return true;
            }
        }

        return false;
    }
};
