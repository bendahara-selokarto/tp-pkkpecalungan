<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuKeuanganReportPrintTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_admin_desa_dapat_mencetak_buku_keuangan_dari_data_transaksi_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        BukuKeuangan::create([
            'transaction_date' => now()->toDateString(),
            'source' => 'pusat',
            'description' => 'Setoran iuran rutin',
            'reference_number' => 'BK-001',
            'entry_type' => 'pemasukan',
            'amount' => 1000000,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.buku-keuangan.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_buku_keuangan_dari_data_transaksi_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        BukuKeuangan::create([
            'transaction_date' => now()->toDateString(),
            'source' => 'kabupaten',
            'description' => 'Belanja ATK sekretariat',
            'reference_number' => 'BK-002',
            'entry_type' => 'pengeluaran',
            'amount' => 1500000,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.buku-keuangan.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pengguna_dengan_role_scope_tidak_valid_ditolak_mengakses_buku_keuangan(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $response = $this->actingAs($user)->get(route('desa.buku-keuangan.report'));

        $response->assertStatus(403);
    }

    public function test_buku_keuangan_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.buku-keuangan.report'));

        $response->assertStatus(403);
    }

    public function test_route_alias_lama_tetap_mengarahkan_ke_report_buku_keuangan_baru(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        BukuKeuangan::create([
            'transaction_date' => now()->toDateString(),
            'source' => 'kas_tunai',
            'description' => 'Kas awal bulan',
            'reference_number' => 'BK-003',
            'entry_type' => 'pemasukan',
            'amount' => 200000,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.bantuans.keuangan.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
