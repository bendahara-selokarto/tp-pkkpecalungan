<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanBantuanTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_daftar_bantuan_di_kecamatannya_sendiri()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Bantuan::create([
            'name' => 'Desa Gombong',
            'category' => 'uang',
            'description' => 'Untuk kecamatan A',
            'source' => 'kabupaten',
            'amount' => 45000000,
            'received_date' => '2026-02-07',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        Bantuan::create([
            'name' => 'Desa Sidomulyo',
            'category' => 'barang',
            'description' => 'Untuk kecamatan B',
            'source' => 'pihak_ketiga',
            'amount' => 15000000,
            'received_date' => '2026-02-08',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/bantuans');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Bantuan/Index')
                ->has('bantuans.data', 1)
                ->where('bantuans.data.0.lokasi_penerima', 'Desa Gombong')
                ->where('bantuans.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_bantuan_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            Bantuan::create([
                'name' => 'Desa A ' . $index,
                'category' => 'uang',
                'description' => 'Untuk kecamatan A',
                'source' => 'kabupaten',
                'amount' => 45000000 + $index,
                'received_date' => now()->subDays($index)->toDateString(),
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        Bantuan::create([
            'name' => 'Desa B Bocor',
            'category' => 'barang',
            'description' => 'Untuk kecamatan B',
            'source' => 'pihak_ketiga',
            'amount' => 15000000,
            'received_date' => now()->toDateString(),
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/bantuans?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Desa B Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Bantuan/Index')
                ->has('bantuans.data', 1)
                ->where('bantuans.current_page', 2)
                ->where('bantuans.per_page', 10)
                ->where('bantuans.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_bantuan_kecamatan_lain()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $bantuanLuar = Bantuan::create([
            'name' => 'Desa Limpung',
            'category' => 'uang',
            'description' => 'Luar wilayah',
            'source' => 'pusat',
            'amount' => 12000000,
            'received_date' => '2026-02-09',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.bantuans.show', $bantuanLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_data_bantuan()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/bantuans', [
            'lokasi_penerima' => 'Desa Randu',
            'jenis_bantuan' => 'uang',
            'keterangan' => 'Tahap awal',
            'asal_bantuan' => 'kabupaten',
            'jumlah' => 5000000,
            'tanggal' => '2026-02-12',
        ])->assertStatus(302);

        $bantuan = Bantuan::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('name', 'Desa Randu')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.bantuans.update', $bantuan->id), [
            'lokasi_penerima' => 'Desa Randu',
            'jenis_bantuan' => 'uang',
            'keterangan' => 'Tahap revisi',
            'asal_bantuan' => 'lainnya',
            'jumlah' => 7000000,
            'tanggal' => '2026-02-20',
        ])->assertStatus(302);

        $this->assertDatabaseHas('bantuans', [
            'id' => $bantuan->id,
            'source' => 'lainnya',
            'amount' => 7000000,
            'received_date' => '2026-02-20',
            'description' => 'Tahap revisi',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_bantuan_kecamatan()
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/bantuans');

        $response->assertStatus(403);
    }
}
