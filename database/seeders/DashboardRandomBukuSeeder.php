<?php

namespace Database\Seeders;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DashboardRandomBukuSeeder extends Seeder
{
    private const TARGET_KECAMATAN = 'Pecalungan';

    /**
     * @var list<string>
     */
    private const DESA_BOOK_TABLES = [
        'anggota_tim_penggeraks',
        'kader_khusus',
        'agenda_surats',
        'buku_keuangans',
        'inventaris',
        'activities',
        'data_wargas',
        'data_kegiatan_wargas',
        'data_keluargas',
        'data_pemanfaatan_tanah_pekarangan_hatinya_pkks',
        'data_industri_rumah_tanggas',
        'data_pelatihan_kaders',
        'warung_pkks',
        'taman_bacaans',
        'koperasis',
        'kejar_pakets',
        'posyandus',
        'simulasi_penyuluhans',
    ];

    private const MIN_ACTIVE_TABLES_PER_DESA = 5;
    private const MAX_ACTIVE_TABLES_PER_DESA = 14;

    public function run(): void
    {
        $this->call([
            DashboardNaturalBatangSeeder::class,
        ]);

        $kecamatanArea = Area::query()
            ->where('level', 'kecamatan')
            ->where('name', self::TARGET_KECAMATAN)
            ->first();

        if (! $kecamatanArea) {
            $this->command?->warn('Seeder random buku dibatalkan: kecamatan target tidak ditemukan.');

            return;
        }

        $desaAreas = Area::query()
            ->where('level', 'desa')
            ->where('parent_id', (int) $kecamatanArea->id)
            ->orderBy('name')
            ->get();

        if ($desaAreas->isEmpty()) {
            $this->command?->warn('Seeder random buku dibatalkan: desa turunan tidak ditemukan.');

            return;
        }

        foreach ($desaAreas as $desaArea) {
            $this->randomizeDesaCoverage((int) $desaArea->id);
        }

        $this->command?->info(sprintf(
            'DashboardRandomBukuSeeder selesai. Coverage buku diacak untuk %d desa di %s.',
            $desaAreas->count(),
            self::TARGET_KECAMATAN
        ));
    }

    private function randomizeDesaCoverage(int $desaAreaId): void
    {
        $tableCount = count(self::DESA_BOOK_TABLES);
        $maxActive = min(self::MAX_ACTIVE_TABLES_PER_DESA, $tableCount);
        $minActive = min(self::MIN_ACTIVE_TABLES_PER_DESA, $maxActive);
        $activeTableCount = random_int($minActive, $maxActive);
        $activeTables = collect(self::DESA_BOOK_TABLES)
            ->shuffle()
            ->take($activeTableCount)
            ->values()
            ->all();

        $creatorId = $this->resolveCreatorId($desaAreaId);

        foreach (self::DESA_BOOK_TABLES as $table) {
            $baseQuery = DB::table($table)
                ->where('level', 'desa')
                ->where('area_id', $desaAreaId);

            $isActive = in_array($table, $activeTables, true);
            if (! $isActive) {
                $baseQuery->delete();
                continue;
            }

            if ($table === 'buku_keuangans' && (clone $baseQuery)->count() === 0) {
                $this->seedRandomBukuKeuangan($desaAreaId, $creatorId);
            }

            $totalRows = (clone $baseQuery)->count();
            if ($totalRows <= 1) {
                continue;
            }

            $keepCount = random_int(1, $totalRows);
            if ($keepCount >= $totalRows) {
                continue;
            }

            $keepIds = (clone $baseQuery)
                ->inRandomOrder()
                ->limit($keepCount)
                ->pluck('id')
                ->all();

            (clone $baseQuery)
                ->whereNotIn('id', $keepIds)
                ->delete();
        }
    }

    private function seedRandomBukuKeuangan(int $desaAreaId, int $creatorId): void
    {
        $insertRows = [];
        $totalRows = random_int(1, 6);

        for ($i = 1; $i <= $totalRows; $i++) {
            $insertRows[] = [
                'transaction_date' => now()->subDays(random_int(0, 180))->toDateString(),
                'source' => BukuKeuangan::SOURCES[array_rand(BukuKeuangan::SOURCES)],
                'description' => sprintf('Transaksi Buku Keuangan #%d', $i),
                'reference_number' => sprintf('BK-%d-%03d', $desaAreaId, $i),
                'entry_type' => BukuKeuangan::ENTRY_TYPES[array_rand(BukuKeuangan::ENTRY_TYPES)],
                'amount' => random_int(100000, 2500000),
                'level' => 'desa',
                'area_id' => $desaAreaId,
                'created_by' => $creatorId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('buku_keuangans')->insert($insertRows);
    }

    private function resolveCreatorId(int $desaAreaId): int
    {
        $creator = User::query()
            ->where('scope', 'desa')
            ->where('area_id', $desaAreaId)
            ->first();

        if ($creator) {
            return (int) $creator->id;
        }

        $fallbackCreator = User::query()
            ->where('area_id', $desaAreaId)
            ->first();

        if ($fallbackCreator) {
            return (int) $fallbackCreator->id;
        }

        $anyUserId = User::query()->value('id');
        if (is_numeric($anyUserId)) {
            return (int) $anyUserId;
        }

        throw new RuntimeException('Seeder random buku membutuhkan minimal satu user sebagai creator.');
    }
}
