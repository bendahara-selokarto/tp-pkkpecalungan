<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ArsipTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_arsip_mewajibkan_autentikasi(): void
    {
        $this->get(route('arsip.index'))
            ->assertRedirect(route('login', absolute: false));
    }

    public function test_pengguna_terautentikasi_dapat_melihat_halaman_arsip(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('arsip.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
                ->component('Arsip/Index')
                ->has('documents')
                ->has('documents.0')
                ->where('documents.0.name', fn ($name): bool => is_string($name) && $name !== '')
                ->where('documents.0.extension', fn ($extension): bool => is_string($extension) && $extension !== '')
                ->where('documents.0.size_bytes', fn ($size): bool => is_numeric($size))
                ->where('documents.0.download_url', fn ($url): bool => is_string($url) && str_starts_with($url, '/arsip/download/')));
    }

    public function test_pengguna_terautentikasi_dapat_mengunduh_dokumen_arsip(): void
    {
        $user = User::factory()->create();
        $documents = app(ArsipDocumentRepositoryInterface::class)->listDocuments();
        $this->assertNotEmpty($documents, 'Dokumen arsip referensi tidak ditemukan pada docs/referensi.');

        $firstDocument = $documents[0];
        $documentName = (string) $firstDocument['name'];

        $this->actingAs($user)
            ->get(route('arsip.download', ['document' => $documentName]))
            ->assertOk()
            ->assertDownload($documentName);
    }

    public function test_download_dokumen_arsip_tidak_valid_mengembalikan_404(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('arsip.download', ['document' => '../rahasia.pdf']))
            ->assertNotFound();
    }
}
