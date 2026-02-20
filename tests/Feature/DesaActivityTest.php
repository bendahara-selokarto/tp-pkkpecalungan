<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Activities\Models\Activity;

class DesaActivityTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desa;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat role desa
        Role::create(['name' => 'admin-desa']);

        // Buat Kecamatan
        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        // Buat Desa
        $this->desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    #[Test]
    public function pengguna_desa_dapat_membuat_kegiatan()
    {
        $user = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope'   => 'desa',
        ]);

        $user->assignRole('admin-desa');
        $this->actingAs($user);

        $response = $this->post('/desa/activities', [
            'title' => 'Musyawarah Desa',
            'nama_petugas' => 'Siti Aminah',
            'jabatan_petugas' => 'Sekretaris',
            'tempat_kegiatan' => 'Balai Desa',
            'uraian' => 'Rapat tahunan',
            'tanda_tangan' => 'Siti Aminah',
            'activity_date' => '12/02/2026',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('activities', [
            'title' => 'Musyawarah Desa',
            'nama_petugas' => 'Siti Aminah',
            'jabatan_petugas' => 'Sekretaris',
            'tempat_kegiatan' => 'Balai Desa',
            'uraian' => 'Rapat tahunan',
            'tanda_tangan' => 'Siti Aminah',
            'description' => 'Rapat tahunan',
            'area_id' => $this->desa->id,
        ]);
    }

    #[Test]
    public function pengguna_desa_hanya_melihat_kegiatannya_sendiri()
    {
        $desaUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope'   => 'desa',
        ]);
        $desaUser->assignRole('admin-desa');

        // Desa lain
        $desaLain = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        // Activity desa user
        Activity::create([
            'title' => 'Desa Gombong Event',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $desaUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        // Activity desa lain
        Activity::create([
            'title' => 'Desa Bandung Event',
            'level' => 'desa',
            'area_id' => $desaLain->id,
            'created_by' => $desaUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->actingAs($desaUser);

        $response = $this->get('/desa/activities');

        $response->assertSee('Desa Gombong Event');
        $response->assertDontSee('Desa Bandung Event');
    }

    #[Test]
    public function pengguna_non_desa_tidak_dapat_mengakses_rute_desa()
    {
        $user = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope'   => 'kecamatan',
        ]);

        $this->actingAs($user);

        $response = $this->get('/desa/activities');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_tetapi_area_bukan_desa_tidak_dapat_mengakses_rute_desa()
    {
        $user = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $user->assignRole('admin-desa');

        $this->actingAs($user);

        $response = $this->get('/desa/activities');

        $response->assertStatus(403);
    }

    #[Test]
    public function tanggal_kegiatan_harus_format_dd_mm_yyyy_bukan_mm_dd_yyyy()
    {
        $user = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);

        $user->assignRole('admin-desa');
        $this->actingAs($user);

        $response = $this->post('/desa/activities', [
            'title' => 'Kegiatan Tidak Valid',
            'description' => 'Uji format tanggal',
            'activity_date' => '02/20/2026',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('activity_date');

        $this->assertDatabaseMissing('activities', [
            'title' => 'Kegiatan Tidak Valid',
        ]);
    }
}
