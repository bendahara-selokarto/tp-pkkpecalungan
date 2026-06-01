<style>
    .metadata-footer {
        position: fixed;
        bottom: -10mm;
        left: 0;
        right: 0;
        font-size: 8px;
        color: #4B5563;
        border-top: 0.1pt solid #9CA3AF;
        padding-top: 2mm;
    }
</style>

<div class="metadata-footer">
    Wilayah: {{ $areaName ?? '-' }} |
    Tahun Anggaran: {{ $budgetYearLabel ?? '-' }} |
    Dicetak oleh: {{ $footerUserName ?? '-' }} |
    Dicetak pada: {{ $footerPrintedAt ?? now()->format('Y-m-d H:i:s') }}
</div>
