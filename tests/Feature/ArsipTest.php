<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArsipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);
    }

    public function test_halaman_arsip_mewajibkan_autentikasi(): void
    {
        $this->get(route('arsip.index'))
            ->assertRedirect(route('login', absolute: false));
    }

    public function test_pengguna_non_super_admin_hanya_melihat_dokumen_publik(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('admin-desa');

        $publishedDocument = ArsipDocument::factory()->create([
            'title' => 'Dokumen Publik',
            'is_published' => true,
            'published_at' => now(),
        ]);
        $draftDocument = ArsipDocument::factory()->create([
            'title' => 'Dokumen Draft',
            'is_published' => false,
            'published_at' => null,
        ]);

        Storage::disk('public')->put($publishedDocument->file_path, 'file publik');
        Storage::disk('public')->put($draftDocument->file_path, 'file draft');

        $this->actingAs($user)
            ->get(route('arsip.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
                ->component('Arsip/Index')
                ->has('documents', 1)
                ->where('documents.0.id', $publishedDocument->id)
                ->where('documents.0.title', 'Dokumen Publik'));
    }

    public function test_pengguna_non_super_admin_dapat_mengunduh_dokumen_publik(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('admin-desa');

        $document = ArsipDocument::factory()->create([
            'original_name' => 'pedoman-publik.pdf',
            'is_published' => true,
            'published_at' => now(),
            'download_count' => 0,
        ]);
        Storage::disk('public')->put($document->file_path, 'konten publik');

        $this->actingAs($user)
            ->get(route('arsip.download', ['arsipDocument' => $document->id]))
            ->assertOk()
            ->assertDownload('pedoman-publik.pdf');

        $this->assertDatabaseHas('arsip_documents', [
            'id' => $document->id,
            'download_count' => 1,
        ]);
    }

    public function test_pengguna_non_super_admin_tidak_dapat_mengunduh_dokumen_draft(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('admin-desa');

        $document = ArsipDocument::factory()->create([
            'is_published' => false,
            'published_at' => null,
        ]);
        Storage::disk('public')->put($document->file_path, 'konten draft');

        $this->actingAs($user)
            ->get(route('arsip.download', ['arsipDocument' => $document->id]))
            ->assertNotFound();
    }

    public function test_super_admin_dapat_mengunduh_dokumen_draft(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $document = ArsipDocument::factory()->create([
            'original_name' => 'draft-internal.pdf',
            'is_published' => false,
            'published_at' => null,
        ]);
        Storage::disk('public')->put($document->file_path, 'konten internal');

        $this->actingAs($user)
            ->get(route('arsip.download', ['arsipDocument' => $document->id]))
            ->assertOk()
            ->assertDownload('draft-internal.pdf');
    }
}
