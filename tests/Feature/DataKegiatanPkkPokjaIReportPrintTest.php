<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class DataKegiatanPkkPokjaIReportPrintTest extends TestCase
{
    use RefreshDatabase;
    use AssertsPdfReportHeaders;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-pokja-i']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-pokja-i']);
        Role::firstOrCreate(['name' => 'desa-pokja-ii']);

        $this->kecamatanA = Area::create(['code' => '2000', 'name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['code' => '3000', 'name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create([
            'code' => '2003',
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        Area::create(['code' => '2001', 'name' => 'Pecalungan', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        Area::create(['code' => '2002', 'name' => 'Bandung', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        Area::create(['code' => '2004', 'name' => 'Randu', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        Area::create(['code' => '2005', 'name' => 'Siguci', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        Area::create(['code' => '2006', 'name' => 'Pretek', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        Area::create(['code' => '2007', 'name' => 'Selokarto', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        Area::create(['code' => '2008', 'name' => 'Gemuh', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        Area::create(['code' => '2009', 'name' => 'Gumawang', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        Area::create(['code' => '2010', 'name' => 'Keniten', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_header_kolom_pdf_data_kegiatan_pkk_pokja_i_tetap_sesuai_pedoman(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.data_kegiatan_pkk_pokja_i_report', [
            'NO',
            'NAMA WILAYAH',
            'JML KADER',
            'PENGHAYATAN DAN PENGAMALAN PANCASILA DAN GOTONG ROYONG',
            'KISAH',
            'KRISAN',
            'KILAS',
            'KTIAT',
            'KISAK',
            'PKBN',
        ]);
    }

    public function test_desa_pokja_i_dapat_mencetak_laporan_pdf_data_kegiatan_pkk_pokja_i_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('desa-pokja-i');

        $response = $this->actingAs($user)->get(route('desa.data-kegiatan-pkk-pokja-i.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_kecamatan_pokja_i_dapat_mencetak_laporan_pdf_data_kegiatan_pkk_pokja_i_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('kecamatan-pokja-i');

        AnggotaPokja::create([
            'nama' => 'Kader Pecalungan',
            'jabatan' => 'Pokja I',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'Menikah',
            'alamat' => 'Desa Pecalungan',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'keterangan' => null,
            'pokja' => 'pokja-i',
            'tahun_anggaran' => now()->year,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        Activity::create([
            'title' => 'KISAH gotong royong',
            'description' => 'volume',
            'uraian' => 'metode sasaran',
            'level' => 'desa',
            'group' => 'pokja-i',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => now()->year,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.data-kegiatan-pkk-pokja-i.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        $items = app(CatatanKeluargaRepositoryInterface::class)
            ->getDataKegiatanPkkPokjaIByLevelAndArea('kecamatan', $this->kecamatanA->id);

        $this->assertCount(10, $items);
        $this->assertSame(['Pecalungan', 'Bandung', 'Gombong', 'Randu', 'Siguci', 'Pretek', 'Selokarto', 'Gemuh', 'Gumawang', 'Keniten'], $items->take(10)->pluck('nama_wilayah')->all());
        $this->assertSame(1, $items->sum('jumlah_kader'));
        $this->assertSame(0, $items->first()['jumlah_kader']);
        $this->assertSame(1, $items->get(2)['jumlah_kader']);
        $this->assertSame(0, $items->get(1)['jumlah_kader']);
    }

    public function test_role_tidak_valid_ditolak_mencetak_laporan_pdf_data_kegiatan_pkk_pokja_i(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('desa-pokja-ii');

        $response = $this->actingAs($user)->get(route('desa.data-kegiatan-pkk-pokja-i.report'));

        $response->assertStatus(403);
    }

    public function test_laporan_pdf_data_kegiatan_pkk_pokja_i_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('desa-pokja-i');

        $response = $this->actingAs($user)->get(route('desa.data-kegiatan-pkk-pokja-i.report'));

        $response->assertStatus(403);
    }
}
