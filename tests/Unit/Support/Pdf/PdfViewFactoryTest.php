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
            ->with(PdfViewFactory::PAPER_SIZE_F4, 'landscape')
            ->andReturnSelf();

        $factory = new PdfViewFactory;
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
            ->with(PdfViewFactory::PAPER_SIZE_F4, 'portrait')
            ->andReturnSelf();

        $factory = new PdfViewFactory;
        $result = $factory->loadView('pdf.sample', ['foo' => 'bar'], 'portrait');

        $this->assertSame($pdfMock, $result);
    }

    public function test_paper_size_pdf_bisa_dioverride_ke_a4_secara_eksplisit(): void
    {
        $pdfMock = Mockery::mock(DompdfPdf::class);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('pdf.sample', ['foo' => 'bar'])
            ->andReturn($pdfMock);

        $pdfMock->shouldReceive('setPaper')
            ->once()
            ->with(PdfViewFactory::PAPER_SIZE_A4, 'landscape')
            ->andReturnSelf();

        $factory = new PdfViewFactory;
        $result = $factory->loadView('pdf.sample', ['foo' => 'bar'], null, PdfViewFactory::PAPER_SIZE_A4);

        $this->assertSame($pdfMock, $result);
    }

    public function test_orientation_pdf_invalid_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = new PdfViewFactory;
        $factory->loadView('pdf.sample', [], 'invalid');
    }
public function test_otomatisasi_metadata_header_saat_user_login(): void
{
    // Mock Role
    $role = Mockery::mock(\Spatie\Permission\Models\Role::class);
    $role->shouldReceive('getAttribute')->with('name')->andReturn('desa-sekretaris');
    $role->shouldReceive('offsetExists')->with('name')->andReturn(true);
    $role->shouldReceive('offsetGet')->with('name')->andReturn('desa-sekretaris');

    // Mock User
    $user = Mockery::mock(\App\Models\User::class)->makePartial();
    $user->shouldReceive('getAttribute')->with('active_budget_year')->andReturn(2026);
    $user->shouldReceive('isDesa')->andReturn(true);
    $user->shouldReceive('isKecamatan')->andReturn(false);
    $user->shouldReceive('getAttribute')->with('roles')->andReturn(collect([$role]));

    // Mock Area
    $areaParent = Mockery::mock(\App\Domains\Wilayah\Models\Area::class);
    $areaParent->shouldReceive('getAttribute')->with('name')->andReturn('Kecamatan Pecalungan');

    $area = Mockery::mock(\App\Domains\Wilayah\Models\Area::class);
    $area->shouldReceive('getAttribute')->with('name')->andReturn('Desa Selokarto');
    $area->shouldReceive('getAttribute')->with('parent')->andReturn($areaParent);

    $user->shouldReceive('getAttribute')->with('area')->andReturn($area);

    $this->actingAs($user);


        $pdfMock = Mockery::mock(DompdfPdf::class);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('pdf.sample', Mockery::on(function ($data) {
                return $data['headerRole'] === 'SEKRETARIS PKK DESA' &&
                       $data['headerVillage'] === 'Desa Selokarto' &&
                       $data['headerKecamatan'] === 'Kecamatan Pecalungan' &&
                       $data['headerYear'] === 2026;
            }))
            ->andReturn($pdfMock);

        $pdfMock->shouldReceive('setPaper')->andReturnSelf();

        $factory = new PdfViewFactory;
        $result = $factory->loadView('pdf.sample', []);

        $this->assertSame($pdfMock, $result);
    }

    public function test_otomatisasi_metadata_header_saat_kecamatan_user_login(): void
    {
        // Mock Role
        $role = Mockery::mock(\Spatie\Permission\Models\Role::class);
        $role->shouldReceive('getAttribute')->with('name')->andReturn('kecamatan-sekretaris');
        $role->shouldReceive('offsetExists')->with('name')->andReturn(true);
        $role->shouldReceive('offsetGet')->with('name')->andReturn('kecamatan-sekretaris');

        // Mock User
        $user = Mockery::mock(\App\Models\User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('active_budget_year')->andReturn(2026);
        $user->shouldReceive('isDesa')->andReturn(false);
        $user->shouldReceive('isKecamatan')->andReturn(true);
        $user->shouldReceive('getAttribute')->with('roles')->andReturn(collect([$role]));

        // Mock Area
        $area = Mockery::mock(\App\Domains\Wilayah\Models\Area::class);
        $area->shouldReceive('getAttribute')->with('name')->andReturn('Kecamatan Pecalungan');
        
        $user->shouldReceive('getAttribute')->with('area')->andReturn($area);

        $this->actingAs($user);

        $pdfMock = Mockery::mock(DompdfPdf::class);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('pdf.sample', Mockery::on(function ($data) {
                return $data['headerRole'] === 'SEKRETARIS PKK KECAMATAN' &&
                       ! isset($data['headerVillage']) &&
                       $data['headerKecamatan'] === 'Kecamatan Pecalungan' &&
                       $data['headerYear'] === 2026;
            }))
            ->andReturn($pdfMock);

        $pdfMock->shouldReceive('setPaper')->andReturnSelf();

        $factory = new PdfViewFactory;
        $result = $factory->loadView('pdf.sample', []);

        $this->assertSame($pdfMock, $result);
    }
}
