<?php

namespace Tests\Feature\Concerns;

trait AssertsPdfReportHeaders
{
    /**
     * Assert table headers in a PDF blade view exist and keep the expected order.
     */
    protected function assertPdfReportHeadersInOrder(string $view, array $headers): void
    {
        $html = view($view, [
            'items' => collect(),
            'level' => 'desa',
            'areaName' => 'Contoh Area',
            'printedBy' => (object) ['name' => 'System Test'],
            'printedAt' => now(),
        ])->render();

        $normalizedContent = $this->normalizeText($html);
        $cursor = 0;

        foreach ($headers as $header) {
            $needle = $this->normalizeText($header);
            $position = strpos($normalizedContent, $needle, $cursor);

            $this->assertNotFalse(
                $position,
                sprintf('Header "%s" tidak ditemukan/urutannya berubah pada view %s.', $header, $view)
            );

            $cursor = $position + strlen($needle);
        }
    }

    private function normalizeText(string $text): string
    {
        $stripped = strip_tags($text);
        $upper = strtoupper($stripped);

        return trim((string) preg_replace('/\s+/u', ' ', $upper));
    }
}
