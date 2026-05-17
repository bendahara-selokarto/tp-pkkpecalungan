@php
    /** @var string|null $footerPlace */
    /** @var string|null $footerDate */
    /** @var string|null $footerChairpersonRole */
    /** @var string|null $footerChairpersonName */
    /** @var string|null $footerRoleLabel */
    /** @var string|null $footerUserName */

    $place = $footerPlace ?? 'Pecalungan';
    $date = $footerDate ?? \Carbon\Carbon::now()->translatedFormat('d F Y');
    
    $chairpersonName = $footerChairpersonName ? strtoupper($footerChairpersonName) : '..........................';
    $userName = $footerUserName ? strtoupper($footerUserName) : '..........................';
@endphp

<style>
    .footer-table {
        width: 100%;
        margin-top: 30px;
        border: none !important;
    }
    .footer-table td {
        border: none !important;
        width: 50%;
        text-align: center;
        vertical-align: top;
        padding: 0 20px;
        line-height: 1.5;
    }
    .footer-space {
        height: 60px;
    }
    .footer-name {
        font-weight: bold;
        text-decoration: underline;
    }
</style>

<table class="footer-table">
    <tr>
        <td>
            Mengetahui,<br>
            {{ $footerChairpersonRole ?? 'KETUA TP PKK' }}
            <div class="footer-space"></div>
            <div class="footer-name">{{ $chairpersonName }}</div>
        </td>
        <td>
            {{ $place }}, {{ $date }}<br>
            {{ $footerRoleLabel ?? 'PETUGAS' }}
            <div class="footer-space"></div>
            <div class="footer-name">{{ $userName }}</div>
        </td>
    </tr>
</table>
