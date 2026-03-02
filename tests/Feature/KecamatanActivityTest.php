<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\UseCases\GetScopedActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListScopedActivitiesUseCase;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class KecamatanActivityTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'kecamatan-sekretaris']);
        Role::create(['name' => 'kecamatan-pokja-i']);
        Role::create(['name' => 'kecamatan-pokja-ii']);
        Role::create(['name' => 'kecamatan-pokja-iii']);
        Role::create(['name' => 'kecamatan-pokja-iv']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_membuat_dan_memperbarui_kegiatan(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/activities', [
            'title' => 'Rapat Koordinasi Kecamatan',
            'nama_petugas' => 'Rini',
            'jabatan_petugas' => 'Sekretaris',
            'tempat_kegiatan' => 'Pendopo Kecamatan',
            'uraian' => 'Rapat bulanan',
            'tanda_tangan' => 'Rini',
            'activity_date' => '2026-02-22',
            'status' => 'draft',
        ])->assertStatus(302);

        $activity = Activity::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('title', 'Rapat Koordinasi Kecamatan')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.activities.update', $activity->id), [
            'title' => 'Rapat Koordinasi Kecamatan Revisi',
            'nama_petugas' => 'Rini',
            'jabatan_petugas' => 'Ketua',
            'tempat_kegiatan' => 'Aula Kecamatan',
            'uraian' => 'Rapat bulanan revisi',
            'tanda_tangan' => 'Rini',
            'activity_date' => '2026-02-23',
            'status' => 'published',
        ])->assertStatus(302);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'title' => 'Rapat Koordinasi Kecamatan Revisi',
            'jabatan_petugas' => 'Ketua',
            'tempat_kegiatan' => 'Aula Kecamatan',
            'uraian' => 'Rapat bulanan revisi',
            'description' => 'Rapat bulanan revisi',
            'activity_date' => '2026-02-23',
            'status' => 'published',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_mengganti_upload_gambar_dan_berkas_pada_kegiatan(): void
    {
        Storage::fake('public');

        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $oldImagePath = sprintf('activities/kecamatan/%d/images/old-image.jpg', $this->kecamatanA->id);
        $oldDocumentPath = sprintf('activities/kecamatan/%d/documents/old-doc.pdf', $this->kecamatanA->id);
        Storage::disk('public')->put($oldImagePath, 'old-image');
        Storage::disk('public')->put($oldDocumentPath, 'old-document');

        $activity = Activity::create([
            'title' => 'Kegiatan Lampiran Lama',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
            'activity_date' => '2026-02-23',
            'status' => 'draft',
            'image_path' => $oldImagePath,
            'document_path' => $oldDocumentPath,
        ]);

        $this->actingAs($adminKecamatan)->post(route('kecamatan.activities.update', $activity->id), [
            '_method' => 'PUT',
            'title' => 'Kegiatan Lampiran Baru',
            'activity_date' => '2026-02-24',
            'status' => 'published',
            'image_upload' => $this->fakeJpegUpload('new-image.jpg'),
            'document_upload' => UploadedFile::fake()->create('new-doc.pdf', 120, 'application/pdf'),
        ])->assertStatus(302);

        $activity->refresh();

        $this->assertNotNull($activity->image_path);
        $this->assertNotNull($activity->document_path);
        $this->assertNotSame($oldImagePath, $activity->image_path);
        $this->assertNotSame($oldDocumentPath, $activity->document_path);
        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertMissing($oldDocumentPath);
        Storage::disk('public')->assertExists($activity->image_path);
        Storage::disk('public')->assertExists($activity->document_path);
    }

    #[Test]
    public function admin_kecamatan_menghapus_kegiatan_juga_menghapus_file_lampiran(): void
    {
        Storage::fake('public');

        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $imagePath = sprintf('activities/kecamatan/%d/images/activity-image.jpg', $this->kecamatanA->id);
        $documentPath = sprintf('activities/kecamatan/%d/documents/activity-document.pdf', $this->kecamatanA->id);
        Storage::disk('public')->put($imagePath, 'image-content');
        Storage::disk('public')->put($documentPath, 'document-content');

        $activity = Activity::create([
            'title' => 'Kegiatan Hapus Lampiran Kecamatan',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
            'activity_date' => '2026-02-28',
            'status' => 'draft',
            'image_path' => $imagePath,
            'document_path' => $documentPath,
        ]);

        $this->actingAs($adminKecamatan)
            ->delete(route('kecamatan.activities.destroy', $activity->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('activities', [
            'id' => $activity->id,
        ]);
        Storage::disk('public')->assertMissing($imagePath);
        Storage::disk('public')->assertMissing($documentPath);
    }

    private function fakeJpegUpload(string $fileName): UploadedFile
    {
        $jpegBinary = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDAREAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAb/xAAVEQEBAAAAAAAAAAAAAAAAAAABAP/aAAwDAQACEAMQAAAAqgD/xAAUEAEAAAAAAAAAAAAAAAAAAAAQ/9oACAEBAAEFAm//xAAUEQEAAAAAAAAAAAAAAAAAAAAQ/9oACAEDAQE/AT//xAAUEQEAAAAAAAAAAAAAAAAAAAAQ/9oACAECAQE/AT//2Q==', true);

        return UploadedFile::fake()->createWithContent($fileName, $jpegBinary === false ? '' : $jpegBinary);
    }

    #[Test]
    public function tanggal_kegiatan_kecamatan_harus_format_yyyy_mm_dd(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->post('/kecamatan/activities', [
            'title' => 'Kegiatan Invalid',
            'description' => 'Uji format',
            'activity_date' => '23/02/2026',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('activity_date');
    }

    #[Test]
    public function daftar_kegiatan_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            Activity::create([
                'title' => 'Kegiatan Kecamatan A ' . $index,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
                'activity_date' => now()->subDays($index)->toDateString(),
                'status' => 'draft',
            ]);
        }

        Activity::create([
            'title' => 'Kegiatan Kecamatan B Tidak Boleh Muncul',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/activities?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Kegiatan Kecamatan B Tidak Boleh Muncul');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Activities/Index')
                ->has('activities.data', 1)
                ->where('activities.current_page', 2)
                ->where('activities.per_page', 10)
                ->where('activities.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function pokja_i_kecamatan_hanya_melihat_kegiatan_pokja_i_pada_kecamatan_yang_sama_via_use_case(): void
    {
        $pokjaIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIUser->assignRole('kecamatan-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIIUser->assignRole('kecamatan-pokja-ii');

        Activity::create([
            'title' => 'Kegiatan Kecamatan Pokja I',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'Kegiatan Kecamatan Pokja II',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->actingAs($pokjaIUser);
        $paginator = app(ListScopedActivitiesUseCase::class)->execute('kecamatan', 50);
        $titles = $paginator->getCollection()->pluck('title')->all();

        $this->assertContains('Kegiatan Kecamatan Pokja I', $titles);
        $this->assertNotContains('Kegiatan Kecamatan Pokja II', $titles);
    }

    #[Test]
    public function pokja_i_kecamatan_tidak_boleh_melihat_detail_kegiatan_pokja_lain_meski_satu_kecamatan(): void
    {
        $pokjaIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIUser->assignRole('kecamatan-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIIUser->assignRole('kecamatan-pokja-ii');

        $activityPokjaII = Activity::create([
            'title' => 'Detail Kecamatan Pokja II',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->actingAs($pokjaIUser);

        try {
            app(GetScopedActivityUseCase::class)->execute($activityPokjaII->id, 'kecamatan');
            $this->fail('Expected HttpException 403 was not thrown.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    #[Test]
    public function sekretaris_kecamatan_hanya_melihat_kegiatan_milik_sendiri_pada_mode_kecamatan(): void
    {
        $sekretarisUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $sekretarisUser->assignRole('kecamatan-sekretaris');

        $pokjaIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIUser->assignRole('kecamatan-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIIUser->assignRole('kecamatan-pokja-ii');

        Activity::create([
            'title' => 'Kegiatan Kec Milik Sekretaris',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $sekretarisUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'Kegiatan Kec Pokja I',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'Kegiatan Kec Pokja II',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($sekretarisUser)->get('/kecamatan/activities');

        $response->assertOk();
        $response->assertSee('Kegiatan Kec Milik Sekretaris');
        $response->assertDontSee('Kegiatan Kec Pokja I');
        $response->assertDontSee('Kegiatan Kec Pokja II');
    }
}
