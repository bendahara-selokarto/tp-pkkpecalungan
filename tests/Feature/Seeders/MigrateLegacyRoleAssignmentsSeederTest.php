<?php

namespace Tests\Feature\Seeders;

use App\Models\User;
use Database\Seeders\MigrateLegacyRoleAssignmentsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MigrateLegacyRoleAssignmentsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrasi_role_legacy_mempertahankan_role_non_legacy_dan_menambah_target_scope(): void
    {
        foreach ([
            'admin-desa',
            'desa-bendahara',
            'desa-pokja-i',
            'desa-sekretaris',
            'kecamatan-sekretaris',
        ] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $desaUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => null,
        ]);
        $desaUser->syncRoles(['admin-desa', 'desa-pokja-i']);

        $kecamatanUser = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => null,
        ]);
        $kecamatanUser->syncRoles(['admin-desa']);

        $bendaharaDesaUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => null,
        ]);
        $bendaharaDesaUser->syncRoles(['desa-bendahara']);

        $controlUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => null,
        ]);
        $controlUser->syncRoles(['desa-pokja-i']);

        (new MigrateLegacyRoleAssignmentsSeeder())->run();

        $this->assertEqualsCanonicalizing(
            ['desa-pokja-i', 'desa-sekretaris'],
            $desaUser->fresh()->getRoleNames()->all()
        );
        $this->assertEqualsCanonicalizing(
            ['kecamatan-sekretaris'],
            $kecamatanUser->fresh()->getRoleNames()->all()
        );
        $this->assertEqualsCanonicalizing(
            ['desa-sekretaris'],
            $bendaharaDesaUser->fresh()->getRoleNames()->all()
        );
        $this->assertEqualsCanonicalizing(
            ['desa-pokja-i'],
            $controlUser->fresh()->getRoleNames()->all()
        );
    }
}
