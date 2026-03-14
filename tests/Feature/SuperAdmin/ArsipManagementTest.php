<?php

namespace Tests\Feature\SuperAdmin;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArsipManagementTest extends TestCase
{
    use RefreshDatabase;

    private Area $kecamatan;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'desa-sekretaris']);

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

    public function test_super_admin_dapat_mengelola_arsip_global_milik_super_admin_lain(): void
    {
        Storage::fake('public');

        $creator = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $creator->assignRole('super-admin');

        $operator = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $operator->assignRole('super-admin');

        $document = ArsipDocument::factory()->create([
            'title' => 'Dokumen Global Lama',
            'description' => 'Dokumen global milik super-admin creator.',
            'is_global' => true,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);

        Storage::disk('public')->put($document->file_path, 'global-file');

        $this->actingAs($operator)
            ->put(route('super-admin.arsip.update', $document), [
                'title' => 'Dokumen Global Diperbarui Operator',
                'description' => 'Perubahan oleh super-admin lain.',
            ])
            ->assertRedirect(route('super-admin.arsip.index'))
            ->assertSessionHas('success');

        $document->refresh();

        $this->assertSame('Dokumen Global Diperbarui Operator', $document->title);
        $this->assertSame($operator->id, $document->updated_by);

        $this->actingAs($operator)
            ->delete(route('super-admin.arsip.destroy', $document))
            ->assertRedirect(route('super-admin.arsip.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('arsip_documents', ['id' => $document->id]);
        Storage::disk('public')->assertMissing($document->file_path);
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
        $user->assignRole('desa-sekretaris');

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
        $staleUser->assignRole('desa-sekretaris');

        $this->actingAs($staleUser)
            ->get(route('super-admin.arsip.index'))
            ->assertStatus(403);
    }

    public function test_super_admin_dapat_mengubah_per_page_arsip_management_ke_nilai_valid(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        ArsipDocument::factory()
            ->count(30)
            ->state([
                'is_global' => true,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatan->id,
                'created_by' => $superAdmin->id,
                'updated_by' => $superAdmin->id,
            ])
            ->create();

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.arsip.index', ['per_page' => 25]));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Arsip/Index')
                ->where('filters.per_page', 25)
                ->where('documents.per_page', 25)
                ->has('documents.data', 25);
        });
    }

    public function test_super_admin_per_page_invalid_arsip_management_fallback_ke_default(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        ArsipDocument::factory()
            ->count(15)
            ->state([
                'is_global' => true,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatan->id,
                'created_by' => $superAdmin->id,
                'updated_by' => $superAdmin->id,
            ])
            ->create();

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.arsip.index', ['per_page' => 999]));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Arsip/Index')
                ->where('filters.per_page', 10)
                ->where('documents.per_page', 10)
                ->has('documents.data', 10);
        });
    }

    public function test_link_pagination_arsip_management_mempertahankan_query_per_page_saat_navigasi_halaman(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        ArsipDocument::factory()
            ->count(30)
            ->state([
                'is_global' => true,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatan->id,
                'created_by' => $superAdmin->id,
                'updated_by' => $superAdmin->id,
            ])
            ->create();

        $pageOneResponse = $this->actingAs($superAdmin)
            ->get(route('super-admin.arsip.index', ['per_page' => 25]));

        $pageOneResponse->assertOk();
        $pageOneResponse->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Arsip/Index')
                ->where('filters.per_page', 25)
                ->where('documents.current_page', 1)
                ->where('documents.next_page_url', static function (mixed $url): bool {
                    if (! is_string($url)) {
                        return false;
                    }

                    return str_contains($url, 'page=2')
                        && str_contains($url, 'per_page=25');
                });
        });

        $pageTwoResponse = $this->actingAs($superAdmin)
            ->get(route('super-admin.arsip.index', ['page' => 2, 'per_page' => 25]));

        $pageTwoResponse->assertOk();
        $pageTwoResponse->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Arsip/Index')
                ->where('filters.per_page', 25)
                ->where('documents.current_page', 2)
                ->has('documents.data', 5);
        });

        $backToPageOneResponse = $this->actingAs($superAdmin)
            ->get(route('super-admin.arsip.index', ['page' => 1, 'per_page' => 25]));

        $backToPageOneResponse->assertOk();
        $backToPageOneResponse->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Arsip/Index')
                ->where('filters.per_page', 25)
                ->where('documents.current_page', 1);
        });
    }
}
