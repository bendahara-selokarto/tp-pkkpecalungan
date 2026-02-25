<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanBukuKeuanganTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_melihat_daftar_buku_keuangan_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        BukuKeuangan::create([
            'transaction_date' => '2026-02-07',
            'source' => 'kabupaten',
            'description' => 'Dana program kecamatan',
            'reference_number' => 'BK-301',
            'entry_type' => 'pemasukan',
            'amount' => 45000000,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        BukuKeuangan::create([
            'transaction_date' => '2026-02-08',
            'source' => 'bank',
            'description' => 'Belanja kecamatan lain',
            'reference_number' => 'BK-302',
            'entry_type' => 'pengeluaran',
            'amount' => 15000000,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/buku-keuangan');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/BukuKeuangan/Index')
                ->has('entries.data', 1)
                ->where('entries.data.0.description', 'Dana program kecamatan')
                ->where('entries.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_buku_keuangan_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            BukuKeuangan::create([
                'transaction_date' => now()->subDays($index)->toDateString(),
                'source' => 'kabupaten',
                'description' => 'Dana program kecamatan ' . $index,
                'reference_number' => 'BK-' . str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                'entry_type' => 'pemasukan',
                'amount' => 45000000 + $index,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        BukuKeuangan::create([
            'transaction_date' => now()->toDateString(),
            'source' => 'bank',
            'description' => 'Belanja kecamatan bocor',
            'reference_number' => 'BK-BOCOR',
            'entry_type' => 'pengeluaran',
            'amount' => 15000000,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/buku-keuangan?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Belanja kecamatan bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/BukuKeuangan/Index')
                ->has('entries.data', 1)
                ->where('entries.current_page', 2)
                ->where('entries.per_page', 10)
                ->where('entries.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_buku_keuangan_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $entryLuar = BukuKeuangan::create([
            'transaction_date' => '2026-02-09',
            'source' => 'bank',
            'description' => 'Biaya luar area',
            'reference_number' => 'BK-401',
            'entry_type' => 'pengeluaran',
            'amount' => 12000000,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.buku-keuangan.show', $entryLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_data_buku_keuangan(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/buku-keuangan', [
            'transaction_date' => '2026-02-12',
            'source' => 'kabupaten',
            'description' => 'Belanja operasional kecamatan',
            'reference_number' => 'BK-501',
            'entry_type' => 'pengeluaran',
            'amount' => 5000000,
        ])->assertStatus(302);

        $entry = BukuKeuangan::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('description', 'Belanja operasional kecamatan')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.buku-keuangan.update', $entry->id), [
            'transaction_date' => '2026-02-20',
            'source' => 'lainnya',
            'description' => 'Belanja operasional revisi',
            'reference_number' => 'BK-501-R',
            'entry_type' => 'pengeluaran',
            'amount' => 7000000,
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_keuangans', [
            'id' => $entry->id,
            'source' => 'lainnya',
            'amount' => 7000000,
            'transaction_date' => '2026-02-20',
            'description' => 'Belanja operasional revisi',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_buku_keuangan_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/buku-keuangan');

        $response->assertStatus(403);
    }
}
