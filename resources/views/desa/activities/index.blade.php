<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Activities</title>
</head>
<body>
    <h1>Desa Activities</h1>
    <a href="{{ url('/desa/activities/create') }}">Tambah Kegiatan</a>

    @forelse ($activities as $activity)
        <div>
            <strong>{{ $activity->title }}</strong>
            <div>{{ $activity->activity_date }} | {{ $activity->status }}</div>
            <a href="{{ url('/desa/activities/' . $activity->id) }}">Lihat</a>
            <a href="{{ url('/desa/activities/' . $activity->id . '/edit') }}">Edit</a>
            <form action="{{ url('/desa/activities/' . $activity->id) }}" method="POST" style="display:inline;">
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
