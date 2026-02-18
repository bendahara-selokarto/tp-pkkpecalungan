<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_dirender(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('Register');
    }

    public function test_root_mengarahkan_tamu_ke_login(): void
    {
        $this->get('/')->assertRedirect(route('login', absolute: false));
    }

    public function test_root_mengarahkan_pengguna_terautentikasi_ke_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_root_mengarahkan_super_admin_ke_halaman_manajemen_pengguna(): void
    {
        Role::create(['name' => 'super-admin']);
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('super-admin.users.index', absolute: false));
    }

    public function test_pengguna_dapat_autentikasi_melalui_halaman_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_pengguna_tidak_dapat_autentikasi_dengan_kata_sandi_tidak_valid(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_pengguna_dapat_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}

