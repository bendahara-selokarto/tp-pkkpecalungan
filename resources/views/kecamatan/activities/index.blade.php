<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kecamatan Activities</title>
</head>
<body>
    <h1>Kecamatan Activities</h1>
    <a href="{{ url('/kecamatan/activities/create') }}">Tambah Kegiatan</a>

    @forelse ($activities as $activity)
        <div>
            <strong>{{ $activity->title }}</strong>
            <div>{{ $activity->activity_date }} | {{ $activity->status }}</div>
            <a href="{{ url('/kecamatan/activities/' . $activity->id) }}">Lihat</a>
            <a href="{{ url('/kecamatan/activities/' . $activity->id . '/edit') }}">Edit</a>
            <form action="{{ url('/kecamatan/activities/' . $activity->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Hapus</button>
            </form>
        </div>
    @empty
        <div>No activities found.</div>
    @endforelse
</body>
</html>
