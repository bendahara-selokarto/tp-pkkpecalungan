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
            'area' => null,
            'budgetYearLabel' => 2026,
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
        $stripped = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5);
        $upper = strtoupper($stripped);

        return trim((string) preg_replace('/\s+/u', ' ', $upper));
    }
}
