<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArsipTest extends TestCase
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

    public function test_halaman_arsip_mewajibkan_autentikasi(): void
    {
        $this->get(route('arsip.index'))
            ->assertRedirect(route('login', absolute: false));
    }

    public function test_pengguna_non_super_admin_hanya_melihat_arsip_global_dan_milik_sendiri(): void
    {
        Storage::fake('public');

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

        $globalDocument = ArsipDocument::factory()->create([
            'title' => 'Pedoman Global',
            'is_global' => true,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => User::factory()->create([
                'scope' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
            ])->id,
        ]);

        $ownedDocument = ArsipDocument::factory()->create([
            'title' => 'Arsip Pribadi Saya',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $owner->id,
        ]);

        ArsipDocument::factory()->create([
            'title' => 'Arsip Pribadi Orang Lain',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $otherUser->id,
        ]);

        Storage::disk('public')->put($globalDocument->file_path, 'global');
        Storage::disk('public')->put($ownedDocument->file_path, 'owned');

        $this->actingAs($owner)
            ->get(route('arsip.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
                ->component('Arsip/Index')
                ->has('documents', 2)
                ->where('documents.0.title', 'Pedoman Global')
                ->where('documents.1.title', 'Arsip Pribadi Saya'));
    }

    public function test_pengguna_dapat_mengunduh_arsip_global(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $user->assignRole('admin-desa');

        $globalDocument = ArsipDocument::factory()->create([
            'original_name' => 'pedoman-global.pdf',
            'is_global' => true,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'download_count' => 0,
            'created_by' => User::factory()->create([
                'scope' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
            ])->id,
        ]);

        Storage::disk('public')->put($globalDocument->file_path, 'global-file');

        $this->actingAs($user)
            ->get(route('arsip.download', ['arsipDocument' => $globalDocument->id]))
            ->assertOk()
            ->assertDownload('pedoman-global.pdf');

        $this->assertDatabaseHas('arsip_documents', [
            'id' => $globalDocument->id,
            'download_count' => 1,
        ]);
    }

    public function test_pengguna_hanya_dapat_mengunduh_arsip_pribadi_milik_sendiri(): void
    {
        Storage::fake('public');

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

        $ownedDocument = ArsipDocument::factory()->create([
            'original_name' => 'arsip-pribadi.pdf',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $owner->id,
        ]);

        Storage::disk('public')->put($ownedDocument->file_path, 'owned-file');

        $this->actingAs($owner)
            ->get(route('arsip.download', ['arsipDocument' => $ownedDocument->id]))
            ->assertOk()
            ->assertDownload('arsip-pribadi.pdf');

        $this->actingAs($otherUser)
            ->get(route('arsip.download', ['arsipDocument' => $ownedDocument->id]))
            ->assertNotFound();
    }

    public function test_sekretaris_kecamatan_dapat_mengunduh_arsip_pribadi_user_desa_di_wilayahnya(): void
    {
        Storage::fake('public');

        $sekretarisKecamatan = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
        $sekretarisKecamatan->assignRole('kecamatan-sekretaris');

        $desaUserInArea = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $desaUserInArea->assignRole('admin-desa');

        $desaUserOutsideArea = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaB->id,
        ]);
        $desaUserOutsideArea->assignRole('admin-desa');

        $inAreaDocument = ArsipDocument::factory()->create([
            'original_name' => 'arsip-gombong.pdf',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $desaUserInArea->id,
        ]);

        $outsideDocument = ArsipDocument::factory()->create([
            'original_name' => 'arsip-kalisalak.pdf',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $desaUserOutsideArea->id,
        ]);

        Storage::disk('public')->put($inAreaDocument->file_path, 'in-area');
        Storage::disk('public')->put($outsideDocument->file_path, 'outside-area');

        $this->actingAs($sekretarisKecamatan)
            ->get(route('arsip.download', ['arsipDocument' => $inAreaDocument->id]))
            ->assertOk()
            ->assertDownload('arsip-gombong.pdf');

        $this->actingAs($sekretarisKecamatan)
            ->get(route('arsip.download', ['arsipDocument' => $outsideDocument->id]))
            ->assertNotFound();
    }

    public function test_super_admin_tidak_dapat_mengunduh_arsip_pribadi_milik_user_lain_dari_jalur_arsip_user(): void
    {
        Storage::fake('public');

        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $desaUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $desaUser->assignRole('admin-desa');

        $privateDocument = ArsipDocument::factory()->create([
            'original_name' => 'arsip-pribadi-desa.pdf',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $desaUser->id,
        ]);

        Storage::disk('public')->put($privateDocument->file_path, 'private-file');

        $this->actingAs($superAdmin)
            ->get(route('arsip.download', ['arsipDocument' => $privateDocument->id]))
            ->assertNotFound();
    }

    public function test_super_admin_tidak_dapat_mutasi_arsip_pribadi_user_lain_dari_jalur_arsip_user(): void
    {
        Storage::fake('public');

        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $desaUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
        ]);
        $desaUser->assignRole('admin-desa');

        $privateDocument = ArsipDocument::factory()->create([
            'title' => 'Arsip Pribadi Desa',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $desaUser->id,
        ]);

        $updatePayload = [
            'title' => 'Judul Diubah Super Admin',
            'description' => 'Update seharusnya ditolak.',
        ];

        $this->actingAs($superAdmin)
            ->put(route('arsip.update', ['arsipDocument' => $privateDocument->id]), $updatePayload)
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->delete(route('arsip.destroy', ['arsipDocument' => $privateDocument->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('arsip_documents', [
            'id' => $privateDocument->id,
            'title' => 'Arsip Pribadi Desa',
        ]);
    }
}
