<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaBukuAgendaSkTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;

    protected Area $desaA;

    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

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

    #[Test]
    public function admin_desa_dapat_crud_dan_list_agenda_sk_terbatas_pada_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        BukuAgendaSk::create([
            'nomor_sk' => 'SK/001',
            'tanggal_sk' => '2026-02-26',
            'kepada' => 'Ketua TP PKK',
            'perihal' => 'Rapat Desa A',
            'tembusan' => 'Sekretariat',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        BukuAgendaSk::create([
            'nomor_sk' => 'SK/002',
            'tanggal_sk' => '2026-02-26',
            'kepada' => 'Ketua TP PKK',
            'perihal' => 'Rapat Desa B',
            'tembusan' => 'Sekretariat',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminDesa)->get('/desa/buku-agenda-sk')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('Desa/BukuAgendaSk/Index')
                    ->has('items.data', 1)
                    ->where('items.data.0.nomor_sk', 'SK/001')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        Storage::fake('public');

        $this->actingAs($adminDesa)->post('/desa/buku-agenda-sk', [
            'nomor_sk' => 'SK/003',
            'tanggal_sk' => '2026-03-01',
            'kepada' => 'Ketua TP PKK',
            'perihal' => 'Rapat Koordinasi',
            'tembusan' => 'Kabid',
            'file' => UploadedFile::fake()->create('agenda-sk.pdf', 100, 'application/pdf'),
        ])->assertStatus(302);

        $created = BukuAgendaSk::query()
            ->where('nomor_sk', 'SK/003')
            ->firstOrFail();

        $this->assertDatabaseHas('buku_agenda_sks', [
            'id' => $created->id,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.buku-agenda-sk.update', $created->id), [
            'nomor_sk' => 'SK/003-REV',
            'tanggal_sk' => '2026-03-02',
            'kepada' => 'Ketua TP PKK',
            'perihal' => 'Rapat Koordinasi Final',
            'tembusan' => 'Kabid',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_agenda_sks', [
            'id' => $created->id,
            'nomor_sk' => 'SK/003-REV',
            'perihal' => 'Rapat Koordinasi Final',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.buku-agenda-sk.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_agenda_sks', ['id' => $created->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_agenda_sk_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        $this->actingAs($adminKecamatan)->get('/desa/buku-agenda-sk')
            ->assertStatus(403);
    }

    #[Test]
    public function metadata_scope_stale_role_desa_dengan_area_kecamatan_ditolak(): void
    {
        $staleUser = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $staleUser->assignRole('desa-sekretaris');

        $this->actingAs($staleUser)->get('/desa/buku-agenda-sk')
            ->assertStatus(403);
    }
}
