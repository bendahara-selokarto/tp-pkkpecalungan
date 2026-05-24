<?php

namespace Tests\Unit\Support\Pdf;

use App\Support\Pdf\AcademicChartPdfService;
use PHPUnit\Framework\TestCase;

class AcademicChartPdfServiceTest extends TestCase
{
    private AcademicChartPdfService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AcademicChartPdfService();
    }

    public function test_it_can_generate_svg_with_valid_structure(): void
    {
        $title = 'Test Chart';
        $labels = ['Label 1', 'Label 2'];
        $series = [
            'Series 1' => [10, 20],
        ];

        $svg = $this->service->generateVerticalBarChart($title, $labels, $series);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('Test Chart', $svg);
        $this->assertStringContainsString('Label 1', $svg);
        $this->assertStringContainsString('Label 2', $svg);
        $this->assertStringContainsString('Series 1', $svg);
        $this->assertStringContainsString('rect', $svg);
        $this->assertStringContainsString('line', $svg);
        $this->assertStringContainsString('text', $svg);
    }

    public function test_it_handles_empty_data_gracefully(): void
    {
        $svg = $this->service->generateVerticalBarChart('Empty', [], []);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('Empty', $svg);
        // Should still render axes
        $this->assertStringContainsString('line', $svg);
    }

    public function test_it_calculates_scale_correctly_for_large_values(): void
    {
        $series = ['Data' => [1200, 500]];
        $svg = $this->service->generateVerticalBarChart('Large Data', ['A', 'B'], $series);

        // Max is 1200, limit should be 1200 (ceil to 100)
        $this->assertStringContainsString('1.200', $svg);
        $this->assertStringContainsString('Large Data', $svg);
    }
}
