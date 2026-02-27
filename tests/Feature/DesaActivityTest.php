<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
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
        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'desa-pokja-i']);
        Role::create(['name' => 'desa-pokja-ii']);
        Role::create(['name' => 'desa-pokja-iii']);
        Role::create(['name' => 'desa-pokja-iv']);

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
            'activity_date' => '2026-02-12',
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
    public function pengguna_desa_dapat_upload_gambar_dan_berkas_pada_kegiatan(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->post('/desa/activities', [
            'title' => 'Kegiatan dengan Lampiran Desa',
            'activity_date' => '2026-02-12',
            'image_upload' => UploadedFile::fake()->image('kegiatan-desa.jpg'),
            'document_upload' => UploadedFile::fake()->create('kegiatan-desa.pdf', 120, 'application/pdf'),
        ]);

        $response->assertStatus(302);

        $activity = Activity::query()
            ->where('title', 'Kegiatan dengan Lampiran Desa')
            ->firstOrFail();

        $this->assertNotNull($activity->image_path);
        $this->assertNotNull($activity->document_path);
        Storage::disk('public')->assertExists($activity->image_path);
        Storage::disk('public')->assertExists($activity->document_path);
    }

    #[Test]
    public function pengguna_desa_menghapus_kegiatan_juga_menghapus_file_lampiran(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $user->assignRole('admin-desa');

        $imagePath = sprintf('activities/desa/%d/images/activity-image.jpg', $this->desa->id);
        $documentPath = sprintf('activities/desa/%d/documents/activity-document.pdf', $this->desa->id);
        Storage::disk('public')->put($imagePath, 'image-content');
        Storage::disk('public')->put($documentPath, 'document-content');

        $activity = Activity::create([
            'title' => 'Kegiatan Hapus Lampiran Desa',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-28',
            'status' => 'draft',
            'image_path' => $imagePath,
            'document_path' => $documentPath,
        ]);

        $this->actingAs($user)
            ->delete(route('desa.activities.destroy', $activity->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('activities', [
            'id' => $activity->id,
        ]);
        Storage::disk('public')->assertMissing($imagePath);
        Storage::disk('public')->assertMissing($documentPath);
    }

    #[Test]
    public function pengguna_desa_dapat_memperbarui_kegiatan()
    {
        $user = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);

        $user->assignRole('admin-desa');
        $this->actingAs($user);

        $activity = Activity::create([
            'title' => 'Kegiatan Awal',
            'nama_petugas' => 'Petugas Awal',
            'jabatan_petugas' => 'Sekretaris',
            'tempat_kegiatan' => 'Balai Desa',
            'uraian' => 'Uraian awal',
            'description' => 'Uraian awal',
            'tanda_tangan' => 'Petugas Awal',
            'activity_date' => '2026-02-12',
            'status' => 'draft',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $user->id,
        ]);

        $response = $this->put(route('desa.activities.update', $activity->id), [
            'title' => 'Kegiatan Revisi',
            'nama_petugas' => 'Petugas Revisi',
            'jabatan_petugas' => 'Ketua',
            'tempat_kegiatan' => 'Aula Desa',
            'uraian' => 'Uraian revisi',
            'tanda_tangan' => 'Petugas Revisi',
            'activity_date' => '2026-02-21',
            'status' => 'published',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'title' => 'Kegiatan Revisi',
            'nama_petugas' => 'Petugas Revisi',
            'jabatan_petugas' => 'Ketua',
            'tempat_kegiatan' => 'Aula Desa',
            'uraian' => 'Uraian revisi',
            'description' => 'Uraian revisi',
            'tanda_tangan' => 'Petugas Revisi',
            'activity_date' => '2026-02-21',
            'status' => 'published',
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
    public function daftar_kegiatan_desa_menggunakan_payload_pagination_dan_tetap_scoped(): void
    {
        $desaUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $desaUser->assignRole('admin-desa');

        $desaLain = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        for ($index = 1; $index <= 12; $index++) {
            Activity::create([
                'title' => 'Kegiatan Desa Gombong ' . $index,
                'level' => 'desa',
                'area_id' => $this->desa->id,
                'created_by' => $desaUser->id,
                'activity_date' => now()->subDays($index)->toDateString(),
                'status' => 'draft',
            ]);
        }

        Activity::create([
            'title' => 'Kegiatan Desa Bandung Bocor',
            'level' => 'desa',
            'area_id' => $desaLain->id,
            'created_by' => $desaUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($desaUser)->get('/desa/activities?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Kegiatan Desa Bandung Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Activities/Index')
                ->has('activities.data', 2)
                ->where('activities.current_page', 2)
                ->where('activities.per_page', 10)
                ->where('activities.total', 12)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_otomatis_kembali_ke_default(): void
    {
        $desaUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $desaUser->assignRole('admin-desa');

        Activity::create([
            'title' => 'Kegiatan Default Per Page',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $desaUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($desaUser)->get('/desa/activities?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Activities/Index')
                ->where('filters.per_page', 10)
                ->where('activities.per_page', 10);
        });
    }

    #[Test]
    public function tanggal_kegiatan_harus_format_yyyy_mm_dd()
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
            'activity_date' => '20/02/2026',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('activity_date');

        $this->assertDatabaseMissing('activities', [
            'title' => 'Kegiatan Tidak Valid',
        ]);
    }

    #[Test]
    public function pokja_i_hanya_melihat_kegiatan_pokja_i_pada_desa_yang_sama(): void
    {
        $pokjaIUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $pokjaIUser->assignRole('desa-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $pokjaIIUser->assignRole('desa-pokja-ii');

        Activity::create([
            'title' => 'Kegiatan Pokja I Selokarto',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $pokjaIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'Kegiatan Pokja II Selokarto',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($pokjaIUser)->get('/desa/activities');

        $response->assertOk();
        $response->assertSee('Kegiatan Pokja I Selokarto');
        $response->assertDontSee('Kegiatan Pokja II Selokarto');
    }

    #[Test]
    public function pokja_i_tidak_boleh_melihat_detail_kegiatan_pokja_lain_meski_satu_desa(): void
    {
        $pokjaIUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $pokjaIUser->assignRole('desa-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $pokjaIIUser->assignRole('desa-pokja-ii');

        $activityPokjaII = Activity::create([
            'title' => 'Detail Pokja II',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($pokjaIUser)->get(route('desa.activities.show', $activityPokjaII->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function sekretaris_desa_tetap_melihat_semua_kegiatan_pada_area_desa_yang_sama(): void
    {
        $sekretarisUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $sekretarisUser->assignRole('desa-sekretaris');

        $pokjaIUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $pokjaIUser->assignRole('desa-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope' => 'desa',
        ]);
        $pokjaIIUser->assignRole('desa-pokja-ii');

        Activity::create([
            'title' => 'Kegiatan Pokja I',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $pokjaIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'Kegiatan Pokja II',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($sekretarisUser)->get('/desa/activities');

        $response->assertOk();
        $response->assertSee('Kegiatan Pokja I');
        $response->assertSee('Kegiatan Pokja II');
    }
}
