<?php

use App\Support\Pdf\PdfViewFactory;

return [
    // Default ukuran dokumen administrasi Indonesia: F4 / folio 215mm x 330mm.
    'default_paper' => PdfViewFactory::PAPER_SIZE_F4,

    // Deployment saat ini hanya melayani satu kecamatan, sehingga identitas wilayah
    // pada formulir PDF yang meminta nama kecamatan/kabupaten/provinsi dibuat tetap.
    'regional_identity' => [
        'kecamatan' => 'Pecalungan',
        'kabupaten' => 'Batang',
        'kab_kota' => 'Batang',
        'provinsi' => 'Jawa Tengah',
    ],
];
