<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Activity #{{ $activity->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            line-height: 1.5;
        }
        .title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .meta {
            margin-bottom: 6px;
        }
        .label {
            font-weight: 700;
            display: inline-block;
            width: 120px;
        }
        .section {
            margin-top: 16px;
        }
        .footer {
            margin-top: 24px;
            font-size: 11px;
            color: #374151;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="title">Laporan Activity</div>

    <div class="meta"><span class="label">Judul</span>: {{ $activity->title }}</div>
    <div class="meta"><span class="label">Level</span>: {{ ucfirst($activity->level) }}</div>
    <div class="meta"><span class="label">Wilayah</span>: {{ $activity->area?->name ?? '-' }}</div>
    <div class="meta"><span class="label">Tanggal</span>: {{ $activity->activity_date }}</div>
    <div class="meta"><span class="label">Status</span>: {{ ucfirst($activity->status) }}</div>
    <div class="meta"><span class="label">Dibuat Oleh</span>: {{ $activity->creator?->name ?? '-' }}</div>

    <div class="section">
        <div class="label">Deskripsi</div>
        <div>{{ $activity->description ?: '-' }}</div>
    </div>

    <div class="footer">
        Dicetak oleh {{ $printedBy?->name ?? '-' }} pada {{ $printedAt->format('Y-m-d H:i:s') }}.
    </div>
</body>
</html>
