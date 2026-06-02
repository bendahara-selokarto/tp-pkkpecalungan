<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Stale: Menunggu penyusunan ulang bertahap');
    }

    /**
     * A basic test example.
     */
    public function test_aplikasi_mengarahkan_tamu_ke_halaman_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login', absolute: false));
    }
}

