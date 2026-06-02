<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

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
    public function test_nilai_benar_adalah_benar(): void
    {
        $this->assertTrue(true);
    }
}

