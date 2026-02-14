<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Activity Desa</title>
</head>
<body>
    <h1>Detail Kegiatan Desa</h1>
    <a href="{{ route('kecamatan.desa-activities.index') }}">Kembali ke Daftar</a>

    <h2>{{ $activity->title }}</h2>
    <p>Desa: {{ $activity->area?->name ?? '-' }}</p>
    <p>{{ $activity->description }}</p>
    <p>Tanggal: {{ $activity->activity_date }}</p>
    <p>Status: {{ $activity->status }}</p>
    <p>Dibuat oleh: {{ $activity->creator?->name ?? '-' }}</p>
</body>
</html>
