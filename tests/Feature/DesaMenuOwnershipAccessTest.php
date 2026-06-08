<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaMenuOwnershipAccessTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    private Area $desaArea;

    protected function setUp(): void
    {
        parent::setUp();

        $this->desaArea = Area::create([
            'name' => 'Desa Test',
            'level' => 'desa',
        ]);

        foreach ([
            RoleScopeMatrix::ROLE_SEKRETARIS_DESA,
            RoleScopeMatrix::ROLE_BENDAHARA_DESA,
            RoleScopeMatrix::ROLE_POKJA_1_DESA,
            RoleScopeMatrix::ROLE_POKJA_2_DESA,
            RoleScopeMatrix::ROLE_POKJA_3_DESA,
            RoleScopeMatrix::ROLE_POKJA_4_DESA,
        ] as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }
    }

    #[Test]
    public function desa_sekretaris_dapat_membuka_buku_bantuan_dan_prestasi_lomba(): void
    {
        $user = $this->createUserWithRole(RoleScopeMatrix::ROLE_SEKRETARIS_DESA);

        $this->actingAs($user)->get('/desa/bantuans')->assertOk();
        $this->actingAs($user)->get('/desa/prestasi-lomba')->assertOk();
    }

    #[Test]
    public function desa_pokja_satu_dapat_membuka_agenda_surat_tugas(): void
    {
        $user = $this->createUserWithRole(RoleScopeMatrix::ROLE_POKJA_1_DESA);

        $this->actingAs($user)->get('/desa/agenda-surat-tugas')->assertOk();
    }

    #[Test]
    public function desa_pokja_dua_dapat_membuka_agenda_surat_tugas(): void
    {
        $user = $this->createUserWithRole(RoleScopeMatrix::ROLE_POKJA_2_DESA);

        $this->actingAs($user)->get('/desa/agenda-surat-tugas')->assertOk();
    }

    #[Test]
    public function desa_pokja_tiga_dapat_membuka_agenda_surat_tugas_dan_buku_kliping(): void
    {
        $user = $this->createUserWithRole(RoleScopeMatrix::ROLE_POKJA_3_DESA);

        $this->actingAs($user)->get('/desa/agenda-surat-tugas')->assertOk();
        $this->actingAs($user)->get('/desa/buku-kliping')->assertOk();
    }

    #[Test]
    public function desa_pokja_empat_dapat_membuka_agenda_surat_tugas(): void
    {
        $user = $this->createUserWithRole(RoleScopeMatrix::ROLE_POKJA_4_DESA);

        $this->actingAs($user)->get('/desa/agenda-surat-tugas')->assertOk();
    }

    #[Test]
    public function desa_bendahara_tetap_ditolak_di_agenda_surat_tugas(): void
    {
        $user = $this->createUserWithRole(RoleScopeMatrix::ROLE_BENDAHARA_DESA);

        $this->actingAs($user)->get('/desa/agenda-surat-tugas')->assertForbidden();
    }

    private function createUserWithRole(string $roleName): User
    {
        $user = User::factory()->create([
            'area_id' => $this->desaArea->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $user->assignRole($roleName);

        return $user;
    }
}
