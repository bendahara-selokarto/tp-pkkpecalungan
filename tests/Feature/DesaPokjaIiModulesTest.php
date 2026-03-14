<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaPokjaIiModulesTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-pokja-ii']);
        Role::firstOrCreate(['name' => 'kecamatan-pokja-ii']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $this->desaB = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    public static function moduleProvider(): array
    {
        return [
            'literasi-warga' => ['literasi-warga', LiterasiWarga::class, [
                'jumlah_tiga_buta' => 4,
            ]],
            'bkb-kegiatan' => ['bkb-kegiatan', BkbKegiatan::class, [
                'jumlah_kelompok' => 2,
                'jumlah_ibu_peserta' => 15,
                'jumlah_ape_set' => 3,
                'jumlah_kelompok_simulasi' => 1,
            ]],
            'tutor-khusus' => ['tutor-khusus', TutorKhusus::class, [
                'jenis_tutor' => 'kf',
                'jumlah_tutor' => 5,
            ]],
            'pelatihan-kader-pokja-ii' => ['pelatihan-kader-pokja-ii', PelatihanKaderPokjaIi::class, [
                'kategori_pelatihan' => 'lp3',
                'jumlah_kader' => 12,
            ]],
            'pra-koperasi-up2k' => ['pra-koperasi-up2k', PraKoperasiUp2k::class, [
                'tingkat' => 'pemula',
                'jumlah_kelompok' => 2,
                'jumlah_peserta' => 20,
            ]],
        ];
    }

    #[Test]
    #[DataProvider('moduleProvider')]
    public function admin_desa_dapat_melihat_data_pokja_ii_di_desanya_sendiri(string $slug, string $modelClass, array $payload): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        $keteranganSendiri = "Keterangan {$slug} Desa A";
        $keteranganLuar = "Keterangan {$slug} Desa B";

        $modelClass::create($this->buildPayload($payload, $adminDesa, $this->desaA, $keteranganSendiri));
        $modelClass::create($this->buildPayload($payload, $adminDesa, $this->desaB, $keteranganLuar));

        $response = $this->actingAs($adminDesa)->get("/desa/{$slug}");

        $response->assertOk();
        $response->assertSee($keteranganSendiri);
        $response->assertDontSee($keteranganLuar);
    }

    #[Test]
    #[DataProvider('moduleProvider')]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_pokja_ii_desa(string $slug): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-pokja-ii');

        $response = $this->actingAs($adminKecamatan)->get("/desa/{$slug}");

        $response->assertStatus(403);
    }

    #[Test]
    #[DataProvider('moduleProvider')]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_pokja_ii_desa(string $slug): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $userStale->assignRole('desa-pokja-ii');

        $response = $this->actingAs($userStale)->get("/desa/{$slug}");

        $response->assertStatus(403);
    }

    private function buildPayload(array $payload, User $creator, Area $area, string $keterangan): array
    {
        return array_merge($payload, [
            'keterangan' => $keterangan,
            'level' => 'desa',
            'area_id' => $area->id,
            'created_by' => $creator->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);
    }
}
