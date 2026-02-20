<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Concerns\ParsesUiDate;
use PHPUnit\Framework\TestCase;

class ParsesUiDateTest extends TestCase
{
    public function test_parse_dd_mm_yyyy_valid_dan_normalisasi_ke_y_m_d(): void
    {
        $parser = $this->makeParser();

        $date = $parser->parse('23/04/1990');

        $this->assertNotNull($date);
        $this->assertSame('1990-04-23', $parser->normalize('23/04/1990'));
    }

    public function test_parse_mm_dd_yyyy_ditolak(): void
    {
        $parser = $this->makeParser();

        $this->assertNull($parser->parse('02/20/2026'));
    }

    public function test_parse_iso_date_ditolak_untuk_input_ui(): void
    {
        $parser = $this->makeParser();

        $this->assertNull($parser->parse('2026-01-31'));
    }

    private function makeParser(): object
    {
        return new class
        {
            use ParsesUiDate;

            public function parse(string $value)
            {
                return $this->parseUiDate($value);
            }

            public function normalize(string $value): string
            {
                return $this->normalizeUiDate($value);
            }
        };
    }
}
