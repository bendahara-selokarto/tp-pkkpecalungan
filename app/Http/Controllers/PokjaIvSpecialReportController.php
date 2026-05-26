<?php

namespace App\Http\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class PokjaIvSpecialReportController extends Controller
{
    public function __construct(
        private readonly PdfViewFactory $pdfViewFactory
    ) {}

    public function printBukuDataUmum(string $scope): Response
    {
        return $this->pdfViewFactory->create('pdf.buku_data_umum_pokja_iv_report', [
            'level' => $scope,
        ])->stream();
    }

    public function printBukuIvaTest(string $scope): Response
    {
        return $this->pdfViewFactory->create('pdf.buku_iva_test_pokja_iv_report', [
            'level' => $scope,
        ])->stream();
    }

    public function printBukuKaderKhusus(string $scope): Response
    {
        return $this->pdfViewFactory->create('pdf.buku_kader_khusus_pokja_iv_report', [
            'level' => $scope,
        ])->stream();
    }

    public function printBukuAsiEksklusif(string $scope): Response
    {
        return $this->pdfViewFactory->create('pdf.buku_asi_eksklusif_pokja_iv_report', [
            'level' => $scope,
        ])->stream();
    }

    public function printBukuDataKegiatanPosyandu(string $scope): Response
    {
        return $this->pdfViewFactory->create('pdf.buku_data_kegiatan_posyandu_pokja_iv_report', [
            'level' => $scope,
        ])->stream();
    }
}
