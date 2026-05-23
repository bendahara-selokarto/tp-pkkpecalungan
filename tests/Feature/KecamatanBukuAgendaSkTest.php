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

class KecamatanBukuAgendaSkTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;

    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Subah',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_crud_dan_list_agenda_sk_terbatas_pada_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        BukuAgendaSk::create([
            'nomor_sk' => 'SK/KEC/001',
            'tanggal_sk' => '2026-02-26',
            'kepada' => 'Ketua TP PKK Desa',
            'perihal' => 'Rapat Kecamatan A',
            'tembusan' => 'Sekretariat',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        BukuAgendaSk::create([
            'nomor_sk' => 'SK/KEC/002',
            'tanggal_sk' => '2026-02-26',
            'kepada' => 'Ketua TP PKK Desa',
            'perihal' => 'Rapat Kecamatan B',
            'tembusan' => 'Sekretariat',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminKecamatan)->get('/kecamatan/buku-agenda-sk')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('Kecamatan/BukuAgendaSk/Index')
                    ->has('items.data', 1)
                    ->where('items.data.0.nomor_sk', 'SK/KEC/001')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        Storage::fake('public');

        $this->actingAs($adminKecamatan)->post('/kecamatan/buku-agenda-sk', [
            'nomor_sk' => 'SK/KEC/003',
            'tanggal_sk' => '2026-03-01',
            'kepada' => 'Ketua TP PKK Desa',
            'perihal' => 'Rapat Koordinasi Kecamatan',
            'tembusan' => 'Kabid',
            'file' => UploadedFile::fake()->create('agenda-sk-kec.pdf', 100, 'application/pdf'),
        ])->assertStatus(302);

        $created = BukuAgendaSk::query()
            ->where('nomor_sk', 'SK/KEC/003')
            ->firstOrFail();

        $this->assertDatabaseHas('buku_agenda_sks', [
            'id' => $created->id,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminKecamatan)->put(route('kecamatan.buku-agenda-sk.update', $created->id), [
            'nomor_sk' => 'SK/KEC/003-REV',
            'tanggal_sk' => '2026-03-02',
            'kepada' => 'Ketua TP PKK Desa',
            'perihal' => 'Rapat Koordinasi Kecamatan Final',
            'tembusan' => 'Kabid',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_agenda_sks', [
            'id' => $created->id,
            'nomor_sk' => 'SK/KEC/003-REV',
            'perihal' => 'Rapat Koordinasi Kecamatan Final',
        ]);

        $this->actingAs($adminKecamatan)->delete(route('kecamatan.buku-agenda-sk.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_agenda_sks', ['id' => $created->id]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_agenda_sk_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        $this->actingAs($adminDesa)->get('/kecamatan/buku-agenda-sk')
            ->assertStatus(403);
    }
}
