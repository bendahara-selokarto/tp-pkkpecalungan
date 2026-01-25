<?php

namespace Tests\Feature\SuperAdmin;

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
    }

   /** @test */
    public function super_admin_cannot_delete_another_super_admin()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Super Admin tidak boleh dihapus');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $target = User::factory()->create();
        $target->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.users.destroy', $target));
    }

}
