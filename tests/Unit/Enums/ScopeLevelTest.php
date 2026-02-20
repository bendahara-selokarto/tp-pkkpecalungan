<?php

namespace Tests\Unit\Enums;

use App\Domains\Wilayah\Enums\ScopeLevel;
use PHPUnit\Framework\TestCase;

class ScopeLevelTest extends TestCase
{
    public function test_report_level_label_desa_terformat_dengan_benar(): void
    {
        $this->assertSame('DESA/KELURAHAN', ScopeLevel::DESA->reportLevelLabel());
    }

    public function test_report_level_label_kecamatan_terformat_dengan_benar(): void
    {
        $this->assertSame('KECAMATAN', ScopeLevel::KECAMATAN->reportLevelLabel());
    }

    public function test_report_area_label_desa_terformat_dengan_benar(): void
    {
        $this->assertSame('Desa/Kelurahan', ScopeLevel::DESA->reportAreaLabel());
    }

    public function test_report_area_label_kecamatan_terformat_dengan_benar(): void
    {
        $this->assertSame('Kecamatan', ScopeLevel::KECAMATAN->reportAreaLabel());
    }
}
