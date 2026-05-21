@php
    /** @var string|null $headerTitle */
    /** @var string|null $headerRole */
    /** @var string|null $headerVillage */
    /** @var string|null $headerKecamatan */
    /** @var string|null $headerYear */
    /** @var string|null $headerLampiran */

    $title = $headerTitle ?? 'LAPORAN';
    $role = ($headerRole ?? null) ? ' ' . strtoupper($headerRole) : '';
    $lampiran = $headerLampiran ?? '';
@endphp

@if($lampiran)
    <div class="lampiran">{{ $lampiran }}</div>
@endif

<div class="title">{{ $title }}{{ $role }}</div>

<div class="meta">
    @if(isset($headerVillage) && $headerVillage)
        Desa: {{ $headerVillage }} | 
    @endif
    Kecamatan: {{ $headerKecamatan ?? '-' }}<br>
    Tahun Anggaran: {{ $headerYear ?? '-' }}
</div>
