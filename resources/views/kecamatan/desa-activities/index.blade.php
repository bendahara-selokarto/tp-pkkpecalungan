<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Activities</title>
</head>
<body>
    <h1>Daftar Kegiatan Desa di Kecamatan</h1>
    <a href="{{ url('/kecamatan/activities') }}">Lihat Kegiatan Kecamatan</a>

    @forelse ($activities as $activity)
        <div>
            <strong>{{ $activity->title }}</strong>
            <div>
                Desa: {{ $activity->area?->name ?? '-' }} |
                Tanggal: {{ $activity->activity_date }} |
                Status: {{ $activity->status }} |
                Dibuat oleh: {{ $activity->creator?->name ?? '-' }}
            </div>
            <a href="{{ route('kecamatan.desa-activities.show', $activity->id) }}">Lihat Detail</a>
        </div>
    @empty
        <div>Belum ada kegiatan desa pada kecamatan ini.</div>
    @endforelse
</body>
</html>
