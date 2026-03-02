<?php

namespace Tests\Feature\SuperAdmin;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArsipManagementTest extends TestCase
{
    use RefreshDatabase;

    private Area $kecamatan;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);
    }

    public function test_super_admin_dapat_mengelola_dokumen_arsip_crud(): void
    {
        Storage::fake('public');

        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $createResponse = $this->actingAs($superAdmin)
            ->post(route('super-admin.arsip.store'), [
                'title' => 'Pedoman Rakernas',
                'description' => 'Dokumen acuan rakernas terbaru.',
                'document_file' => UploadedFile::fake()->create('pedoman-rakernas.pdf', 120, 'application/pdf'),
            ]);

        $createResponse
            ->assertRedirect(route('super-admin.arsip.index'))
            ->assertSessionHas('success');

        $document = ArsipDocument::query()->first();
        $this->assertNotNull($document);
        $this->assertTrue($document->is_global);
        Storage::disk('public')->assertExists($document->file_path);

        $oldPath = $document->file_path;

        $updateResponse = $this->actingAs($superAdmin)
            ->put(route('super-admin.arsip.update', $document), [
                'title' => 'Pedoman Rakernas Revisi',
                'description' => 'Dokumen revisi internal.',
                'document_file' => UploadedFile::fake()->create('pedoman-rakernas-revisi.pdf', 140, 'application/pdf'),
            ]);

        $updateResponse
            ->assertRedirect(route('super-admin.arsip.index'))
            ->assertSessionHas('success');

        $document->refresh();
        $this->assertSame('Pedoman Rakernas Revisi', $document->title);
        $this->assertTrue($document->is_global);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($document->file_path);

        $storedPath = $document->file_path;

        $deleteResponse = $this->actingAs($superAdmin)
            ->delete(route('super-admin.arsip.destroy', $document));

        $deleteResponse
            ->assertRedirect(route('super-admin.arsip.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('arsip_documents', ['id' => $document->id]);
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_non_super_admin_ditolak_mengakses_management_arsip(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('admin-desa');

        $this->actingAs($user)
            ->get(route('super-admin.arsip.index'))
            ->assertStatus(403);
    }

    public function test_non_super_admin_dengan_metadata_wilayah_stale_tetap_ditolak_mengakses_management_arsip(): void
    {
        $staleUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->kecamatan->id,
        ]);
        $staleUser->assignRole('admin-desa');

        $this->actingAs($staleUser)
            ->get(route('super-admin.arsip.index'))
            ->assertStatus(403);
    }
}
