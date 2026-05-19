<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Support\RoleScopeMatrix;
use Mockery;
use PHPUnit\Framework\TestCase;

class ActivityPolicyTest extends TestCase
{
    private ActivityPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ActivityPolicy();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createUser(string $role): User
    {
        $user = new User();
        $user->role = $role;
        return $user;
    }

    public function test_super_admin_has_full_access(): void
    {
        $user = $this->createUser(RoleScopeMatrix::ROLE_SUPER_ADMIN);

        $this->assertTrue($this->policy->viewAny($user));
        $this->assertTrue($this->policy->create($user));
    }

    public function test_pokja_1_desa_has_activities_access(): void
    {
        $user = $this->createUser(RoleScopeMatrix::ROLE_POKJA_1_DESA);

        $this->assertTrue($this->policy->viewAny($user));
        $this->assertTrue($this->policy->create($user));
    }

    public function test_unauthorized_role_denied(): void
    {
        $user = $this->createUser('tamu');

        $this->assertFalse($this->policy->viewAny($user));
        $this->assertFalse($this->policy->create($user));
    }

    public function test_admin_pusat_has_view_only_activities(): void
    {
        $user = $this->createUser(RoleScopeMatrix::ROLE_ADMIN_PUSAT);

        $this->assertTrue($this->policy->viewAny($user));
        $this->assertFalse($this->policy->create($user));
    }
}
