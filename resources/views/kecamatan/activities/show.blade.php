<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Detail</title>
</head>
<body>
    <h1>Detail Kegiatan Kecamatan</h1>
    <a href="{{ url('/kecamatan/activities') }}">Kembali</a>

    <h2>{{ $activity->title }}</h2>
    <p>{{ $activity->description }}</p>
    <p>Tanggal: {{ $activity->activity_date }}</p>
    <p>Status: {{ $activity->status }}</p>

    <a href="{{ url('/kecamatan/activities/' . $activity->id . '/edit') }}">Edit</a>
</body>
</html>
