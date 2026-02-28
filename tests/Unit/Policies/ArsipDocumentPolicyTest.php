<?php

namespace Tests\Unit\Policies;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Models\User;
use App\Policies\ArsipDocumentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArsipDocumentPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);
    }

    #[Test]
    public function super_admin_memiliki_akses_penuh_management_arsip(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $document = ArsipDocument::factory()->create([
            'is_published' => false,
            'published_at' => null,
        ]);

        $policy = app(ArsipDocumentPolicy::class);

        $this->assertTrue($policy->viewAny($superAdmin));
        $this->assertTrue($policy->view($superAdmin, $document));
        $this->assertTrue($policy->create($superAdmin));
        $this->assertTrue($policy->update($superAdmin, $document));
        $this->assertTrue($policy->delete($superAdmin, $document));
    }

    #[Test]
    public function non_super_admin_hanya_bisa_melihat_dokumen_publik(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin-desa');

        $publishedDocument = ArsipDocument::factory()->create([
            'is_published' => true,
            'published_at' => now(),
        ]);
        $draftDocument = ArsipDocument::factory()->create([
            'is_published' => false,
            'published_at' => null,
        ]);

        $policy = app(ArsipDocumentPolicy::class);

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->view($user, $publishedDocument));
        $this->assertFalse($policy->view($user, $draftDocument));
        $this->assertFalse($policy->create($user));
        $this->assertFalse($policy->update($user, $publishedDocument));
        $this->assertFalse($policy->delete($user, $publishedDocument));
    }
}
