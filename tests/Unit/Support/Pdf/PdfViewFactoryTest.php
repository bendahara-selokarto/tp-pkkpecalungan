<?php

namespace Tests\Unit\Support\Pdf;

use App\Support\Pdf\PdfViewFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DompdfPdf;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class PdfViewFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_default_orientation_pdf_adalah_landscape(): void
    {
        $pdfMock = Mockery::mock(DompdfPdf::class);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('pdf.sample', ['foo' => 'bar'])
            ->andReturn($pdfMock);

        $pdfMock->shouldReceive('setPaper')
            ->once()
            ->with('a4', 'landscape')
            ->andReturnSelf();

        $factory = new PdfViewFactory();
        $result = $factory->loadView('pdf.sample', ['foo' => 'bar']);

        $this->assertSame($pdfMock, $result);
    }

    public function test_orientation_pdf_bisa_dioverride_ke_portrait_secara_eksplisit(): void
    {
        $pdfMock = Mockery::mock(DompdfPdf::class);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('pdf.sample', ['foo' => 'bar'])
            ->andReturn($pdfMock);

        $pdfMock->shouldReceive('setPaper')
            ->once()
            ->with('a4', 'portrait')
            ->andReturnSelf();

        $factory = new PdfViewFactory();
        $result = $factory->loadView('pdf.sample', ['foo' => 'bar'], 'portrait');

        $this->assertSame($pdfMock, $result);
    }

    public function test_orientation_pdf_invalid_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = new PdfViewFactory();
        $factory->loadView('pdf.sample', [], 'invalid');
    }
}
