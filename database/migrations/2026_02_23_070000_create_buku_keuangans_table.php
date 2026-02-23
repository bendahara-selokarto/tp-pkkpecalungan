<?php

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_keuangans', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->enum('source', BukuKeuangan::SOURCES);
            $table->string('description');
            $table->string('reference_number')->nullable();
            $table->enum('entry_type', BukuKeuangan::ENTRY_TYPES);
            $table->decimal('amount', 15, 2);
            $table->enum('level', ['desa', 'kecamatan']);
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'area_id']);
            $table->index(['transaction_date', 'entry_type']);
        });

        $this->backfillFromLegacyBantuanTable();
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_keuangans');
    }

    private function backfillFromLegacyBantuanTable(): void
    {
        if (! Schema::hasTable('bantuans')) {
            return;
        }

        $batch = [];

        DB::table('bantuans')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$batch): void {
                foreach ($rows as $row) {
                    $category = strtolower((string) ($row->category ?? ''));
                    if (! $this->isLegacyKeuanganCategory($category)) {
                        continue;
                    }

                    $entryType = $this->isLegacyPengeluaranCategory($category)
                        ? BukuKeuangan::ENTRY_TYPE_PENGELUARAN
                        : BukuKeuangan::ENTRY_TYPE_PEMASUKAN;

                    $amount = abs((float) $row->amount);
                    if ((float) $row->amount < 0) {
                        $entryType = BukuKeuangan::ENTRY_TYPE_PENGELUARAN;
                    }

                    $source = (string) ($row->source ?? '');
                    if (! in_array($source, BukuKeuangan::SOURCES, true)) {
                        $source = BukuKeuangan::SOURCE_LAINNYA;
                    }

                    $batch[] = [
                        'transaction_date' => $row->received_date,
                        'source' => $source,
                        'description' => (string) ($row->name ?? '-'),
                        'reference_number' => null,
                        'entry_type' => $entryType,
                        'amount' => $amount,
                        'level' => $row->level,
                        'area_id' => $row->area_id,
                        'created_by' => $row->created_by,
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => $row->updated_at ?? now(),
                    ];
                }

                if (count($batch) >= 500) {
                    DB::table('buku_keuangans')->insert($batch);
                    $batch = [];
                }
            });

        if ($batch !== []) {
            DB::table('buku_keuangans')->insert($batch);
        }
    }

    private function isLegacyKeuanganCategory(string $category): bool
    {
        return str_contains($category, 'keuangan')
            || str_contains($category, 'uang')
            || str_contains($category, 'kas')
            || $this->isLegacyPengeluaranCategory($category);
    }

    private function isLegacyPengeluaranCategory(string $category): bool
    {
        return str_contains($category, 'pengeluaran')
            || str_contains($category, 'keluar')
            || str_contains($category, 'belanja')
            || str_contains($category, 'debit');
    }
};
