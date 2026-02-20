<?php

namespace Tests\Feature\SuperAdmin;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);
    }

   #[Test]
    public function super_admin_tidak_dapat_menghapus_super_admin_lain()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $target = User::factory()->create();
        $target->assignRole('super-admin');

        $response = $this->actingAs($superAdmin)
            ->delete(route('super-admin.users.destroy', $target));

        $response
            ->assertRedirect(route('super-admin.users.index'))
            ->assertSessionHas('error', 'Super Admin tidak boleh dihapus');

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
        ]);
    }

    #[Test]
    public function super_admin_tidak_dapat_memperbarui_super_admin_lain()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $target = User::factory()->create([
            'name' => 'Super Admin Target',
            'email' => 'super-admin-target@example.com',
        ]);
        $target->assignRole('super-admin');

        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
        ]);

        $response = $this->actingAs($superAdmin)
            ->put(route('super-admin.users.update', $target), [
                'name' => 'Nama Baru',
                'email' => 'baru@example.com',
                'scope' => 'desa',
                'area_id' => $desa->id,
                'role' => 'admin-desa',
            ]);

        $response
            ->assertRedirect(route('super-admin.users.index'))
            ->assertSessionHas('error', 'Super Admin tidak boleh diubah');

        $target->refresh();

        $this->assertSame('Super Admin Target', $target->name);
        $this->assertSame('super-admin-target@example.com', $target->email);
        $this->assertTrue($target->hasRole('super-admin'));
    }

}

