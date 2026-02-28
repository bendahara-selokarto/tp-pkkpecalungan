<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\ArsipDocumentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArsipDocumentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private Area $kecamatanA;

    private Area $kecamatanB;

    private Area $desaA;

    private Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin-desa', 'kecamatan-sekretaris', 'admin-kecamatan'] as $roleName) {
            Role::create(['name' => $roleName]);
        }

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);

        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $this->desaB = Area::create([
            'name' => 'Kalisalak',
            'level' => 'desa',
            'parent_id' => $this->kecamatanB->id,
        ]);
    }

    #[Test]
    public function owner_dapat_mengelola_arsip_pribadinya(): void
    {
        $owner = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $owner->assignRole('admin-desa');

        $document = ArsipDocument::factory()->create([
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $owner->id,
        ]);

        $policy = app(ArsipDocumentPolicy::class);

        $this->assertTrue($policy->create($owner));
        $this->assertTrue($policy->view($owner, $document));
        $this->assertTrue($policy->update($owner, $document));
        $this->assertTrue($policy->delete($owner, $document));
    }

    #[Test]
    public function non_owner_tidak_dapat_melihat_arsip_pribadi_user_lain(): void
    {
        $owner = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $owner->assignRole('admin-desa');

        $otherUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $otherUser->assignRole('admin-desa');

        $document = ArsipDocument::factory()->create([
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $owner->id,
        ]);

        $policy = app(ArsipDocumentPolicy::class);

        $this->assertFalse($policy->view($otherUser, $document));
        $this->assertFalse($policy->update($otherUser, $document));
        $this->assertFalse($policy->delete($otherUser, $document));
    }

    #[Test]
    public function arsip_global_dapat_dilihat_semua_user(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $user->assignRole('admin-desa');

        $creator = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
        $creator->assignRole('super-admin');

        $document = ArsipDocument::factory()->create([
            'is_global' => true,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $creator->id,
        ]);

        $policy = app(ArsipDocumentPolicy::class);

        $this->assertTrue($policy->view($user, $document));
    }

    #[Test]
    public function sekretaris_kecamatan_dapat_melihat_arsip_desa_di_wilayahnya_saja(): void
    {
        $sekretaris = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
        $sekretaris->assignRole('kecamatan-sekretaris');

        $desaUserA = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $desaUserA->assignRole('admin-desa');

        $desaUserB = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaB->id,
        ]);
        $desaUserB->assignRole('admin-desa');

        $inAreaDocument = ArsipDocument::factory()->create([
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $desaUserA->id,
        ]);

        $outsideDocument = ArsipDocument::factory()->create([
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $desaUserB->id,
        ]);

        $policy = app(ArsipDocumentPolicy::class);

        $this->assertTrue($policy->view($sekretaris, $inAreaDocument));
        $this->assertFalse($policy->view($sekretaris, $outsideDocument));
    }
}
