<?php

namespace Tests\Unit\Support;

use App\Support\RoleLabelFormatter;
use PHPUnit\Framework\TestCase;

class RoleLabelFormatterTest extends TestCase
{
    public function test_label_role_scope_desa_terformat_dengan_benar(): void
    {
        $this->assertSame('Sekretaris (Desa)', RoleLabelFormatter::label('desa-sekretaris'));
        $this->assertSame('Pokja IV (Desa)', RoleLabelFormatter::label('desa-pokja-iv'));
    }

    public function test_label_role_scope_kecamatan_terformat_dengan_benar(): void
    {
        $this->assertSame('Bendahara (Kecamatan)', RoleLabelFormatter::label('kecamatan-bendahara'));
        $this->assertSame('Pokja II (Kecamatan)', RoleLabelFormatter::label('kecamatan-pokja-ii'));
    }

    public function test_label_super_admin_terformat_dengan_benar(): void
    {
        $this->assertSame('Super Admin', RoleLabelFormatter::label('super-admin'));
    }
}

