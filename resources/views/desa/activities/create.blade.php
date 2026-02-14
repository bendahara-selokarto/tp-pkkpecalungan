<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Activity</title>
</head>
<body>
    <h1>Tambah Kegiatan Desa</h1>
    <a href="{{ url('/desa/activities') }}">Kembali</a>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ url('/desa/activities') }}" method="POST">
        @csrf
        <div>
            <label>Judul</label>
            <input type="text" name="title" value="{{ old('title') }}" required>
        </div>
        <div>
            <label>Deskripsi</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>
        <div>
            <label>Tanggal</label>
            <input type="date" name="activity_date" value="{{ old('activity_date') }}" required>
        </div>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>
