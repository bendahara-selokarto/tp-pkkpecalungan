<?php

require __DIR__ . '/../vendor/autoload.php';

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Dompdf\Dompdf;
use Dompdf\Options;

$sourceDir = __DIR__ . '/../docs/user-guide';
$outputDir = __DIR__ . '/../docs/user-guide/pdf';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$files = glob("$sourceDir/*.md");

$converter = new GithubFlavoredMarkdownConverter([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

foreach ($files as $file) {
    $filename = basename($file, '.md');
    echo "Converting $filename.md to PDF...\n";

    $markdown = file_get_contents($file);
    $htmlContent = $converter->convert($markdown)->getContent();

    // Wrap in basic HTML structure with some styling
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; }
            h1 { color: #0891b2; border-bottom: 2px solid #0891b2; padding-bottom: 10px; }
            h2 { color: #164e63; margin-top: 20px; border-bottom: 1px solid #ddd; }
            h3 { color: #1e293b; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { bg-color: #f8fafc; }
            code { background: #f1f5f9; padding: 2px 4px; border-radius: 4px; font-family: monospace; }
            pre { background: #f1f5f9; padding: 10px; border-radius: 4px; }
            hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
        </style>
    </head>
    <body>
        $htmlContent
    </body>
    </html>
    ";

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $outputFile = "$outputDir/$filename.pdf";
    file_put_contents($outputFile, $dompdf->output());
    echo "Saved to $outputFile\n";
}

echo "\nConversion completed successfully!\n";
