<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanReportReverseAreaMismatchTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    #[DataProvider('kecamatanReportRouteProvider')]
    public function test_role_kecamatan_tetapi_area_level_desa_ditolak_di_route_report_kecamatan(string $routeName): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->desaA->id,
        ]);
        $user->assignRole('admin-kecamatan');

        $response = $this->actingAs($user)->get(route($routeName));

        $response->assertStatus(403);
    }

    public static function kecamatanReportRouteProvider(): array
    {
        return [
            ['kecamatan.anggota-tim-penggerak.report'],
            ['kecamatan.kader-khusus.report'],
            ['kecamatan.agenda-surat.report'],
            ['kecamatan.agenda-surat.ekspedisi.report'],
            ['kecamatan.bantuans.keuangan.report'],
            ['kecamatan.inventaris.report'],
            ['kecamatan.data-warga.report'],
            ['kecamatan.data-kegiatan-warga.report'],
            ['kecamatan.data-keluarga.report'],
            ['kecamatan.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.report'],
            ['kecamatan.data-industri-rumah-tangga.report'],
            ['kecamatan.data-pelatihan-kader.report'],
            ['kecamatan.warung-pkk.report'],
            ['kecamatan.taman-bacaan.report'],
            ['kecamatan.koperasi.report'],
            ['kecamatan.kejar-paket.report'],
            ['kecamatan.posyandu.report'],
            ['kecamatan.simulasi-penyuluhan.report'],
            ['kecamatan.catatan-keluarga.report'],
        ];
    }
}
