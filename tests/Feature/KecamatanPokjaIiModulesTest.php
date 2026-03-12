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

class KecamatanPokjaIiModulesTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desa;

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

        $this->desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);
    }

    public static function moduleProvider(): array
    {
        return [
            'literasi-warga' => ['literasi-warga', LiterasiWarga::class, [
                'jumlah_tiga_buta' => 6,
            ]],
            'bkb-kegiatan' => ['bkb-kegiatan', BkbKegiatan::class, [
                'jumlah_kelompok' => 3,
                'jumlah_ibu_peserta' => 22,
                'jumlah_ape_set' => 4,
                'jumlah_kelompok_simulasi' => 2,
            ]],
            'tutor-khusus' => ['tutor-khusus', TutorKhusus::class, [
                'jenis_tutor' => 'paud',
                'jumlah_tutor' => 8,
            ]],
            'pelatihan-kader-pokja-ii' => ['pelatihan-kader-pokja-ii', PelatihanKaderPokjaIi::class, [
                'kategori_pelatihan' => 'tpk_3_pkk',
                'jumlah_kader' => 14,
            ]],
            'pra-koperasi-up2k' => ['pra-koperasi-up2k', PraKoperasiUp2k::class, [
                'tingkat' => 'madya',
                'jumlah_kelompok' => 4,
                'jumlah_peserta' => 26,
            ]],
        ];
    }

    #[Test]
    #[DataProvider('moduleProvider')]
    public function admin_kecamatan_dapat_melihat_data_pokja_ii_di_kecamatannya_sendiri(string $slug, string $modelClass, array $payload): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $keteranganSendiri = "Keterangan {$slug} Kecamatan A";
        $keteranganLuar = "Keterangan {$slug} Kecamatan B";

        $modelClass::create($this->buildPayload($payload, $adminKecamatan, $this->kecamatanA, $keteranganSendiri));
        $modelClass::create($this->buildPayload($payload, $adminKecamatan, $this->kecamatanB, $keteranganLuar));

        $response = $this->actingAs($adminKecamatan)->get("/kecamatan/{$slug}");

        $response->assertOk();
        $response->assertSee($keteranganSendiri);
        $response->assertDontSee($keteranganLuar);
    }

    #[Test]
    #[DataProvider('moduleProvider')]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_pokja_ii_kecamatan(string $slug): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get("/kecamatan/{$slug}");

        $response->assertStatus(403);
    }

    #[Test]
    #[DataProvider('moduleProvider')]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_pokja_ii_kecamatan(string $slug): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $userStale->assignRole('admin-kecamatan');

        $response = $this->actingAs($userStale)->get("/kecamatan/{$slug}");

        $response->assertStatus(403);
    }

    private function buildPayload(array $payload, User $creator, Area $area, string $keterangan): array
    {
        return array_merge($payload, [
            'keterangan' => $keterangan,
            'level' => 'kecamatan',
            'area_id' => $area->id,
            'created_by' => $creator->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);
    }
}
