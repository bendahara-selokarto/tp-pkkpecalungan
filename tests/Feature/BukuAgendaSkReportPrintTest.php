<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuAgendaSkReportPrintTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;

    protected Area $desa;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    #[Test]
    public function admin_desa_dapat_mencetak_laporan_pdf_buku_agenda_sk(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desa->id,
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
            'area_id' => $this->desa->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/buku-agenda-sk/report/pdf');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function admin_kecamatan_dapat_mencetak_laporan_pdf_buku_agenda_sk(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        BukuAgendaSk::create([
            'nomor_sk' => 'SK/KEC/001',
            'tanggal_sk' => '2026-02-26',
            'kepada' => 'Ketua TP PKK Desa',
            'perihal' => 'Rapat Kecamatan',
            'tembusan' => 'Sekretariat',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/buku-agenda-sk/report/pdf');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
