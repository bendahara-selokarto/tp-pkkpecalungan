<?php


use PHPUnit\\Framework\\Attributes\\Test;

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

   #[Test]
    public function super_admin_tidak_dapat_menghapus_super_admin_lain()
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


