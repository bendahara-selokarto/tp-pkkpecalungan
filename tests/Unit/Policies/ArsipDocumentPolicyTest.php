<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\ArsipDocumentPolicy;
use App\Support\RoleScopeMatrix;
use Mockery;
use PHPUnit\Framework\TestCase;

class ArsipDocumentPolicyTest extends TestCase
{
    private ArsipDocumentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ArsipDocumentPolicy();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper to create a user instance with a specific role.
     * We use a real instance but avoid hitting the database for pure unit tests.
     */
    private function createMockUser(string $role): User
    {
        $user = new User();
        $user->role = $role;
        return $user;
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $user = $this->createMockUser(RoleScopeMatrix::ROLE_SUPER_ADMIN);

        $this->assertTrue($this->policy->viewAny($user));
        $this->assertTrue($this->policy->view($user));
        $this->assertTrue($this->policy->create($user));
        $this->assertTrue($this->policy->update($user));
        $this->assertTrue($this->policy->delete($user));
        $this->assertTrue($this->policy->export($user));
    }

    public function test_admin_pusat_has_all_permissions(): void
    {
        $user = $this->createMockUser(RoleScopeMatrix::ROLE_ADMIN_PUSAT);

        $this->assertTrue($this->policy->viewAny($user));
        $this->assertTrue($this->policy->view($user));
        $this->assertTrue($this->policy->create($user));
        $this->assertTrue($this->policy->update($user));
        $this->assertTrue($this->policy->delete($user));
        $this->assertTrue($this->policy->export($user));
    }

    public function test_admin_dusun_cannot_export(): void
    {
        $user = $this->createMockUser(RoleScopeMatrix::ROLE_ADMIN_DUSUN);

        $this->assertTrue($this->policy->viewAny($user));
        $this->assertTrue($this->policy->view($user));
        $this->assertTrue($this->policy->create($user));
        $this->assertTrue($this->policy->update($user));
        $this->assertTrue($this->policy->delete($user));
        $this->assertFalse($this->policy->export($user));
    }

    public function test_unauthorized_role_has_no_permissions(): void
    {
        $user = $this->createMockUser('tamu');

        $this->assertFalse($this->policy->viewAny($user));
        $this->assertFalse($this->policy->view($user));
        $this->assertFalse($this->policy->create($user));
        $this->assertFalse($this->policy->update($user));
        $this->assertFalse($this->policy->delete($user));
        $this->assertFalse($this->policy->export($user));
    }

    public function test_admin_desa_can_export(): void
    {
        $user = $this->createMockUser(RoleScopeMatrix::ROLE_ADMIN_DESA);

        $this->assertTrue($this->policy->export($user));
    }
}
