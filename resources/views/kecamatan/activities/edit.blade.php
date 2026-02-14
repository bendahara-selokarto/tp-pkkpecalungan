<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Activity</title>
</head>
<body>
    <h1>Edit Kegiatan Kecamatan</h1>
    <a href="{{ url('/kecamatan/activities') }}">Kembali</a>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ url('/kecamatan/activities/' . $activity->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label>Judul</label>
            <input type="text" name="title" value="{{ old('title', $activity->title) }}" required>
        </div>
        <div>
            <label>Deskripsi</label>
            <textarea name="description">{{ old('description', $activity->description) }}</textarea>
        </div>
        <div>
            <label>Tanggal</label>
            <input type="date" name="activity_date" value="{{ old('activity_date', $activity->activity_date) }}" required>
        </div>
        <div>
            <label>Status</label>
            <select name="status" required>
                <option value="draft" {{ old('status', $activity->status) === 'draft' ? 'selected' : '' }}>draft</option>
                <option value="published" {{ old('status', $activity->status) === 'published' ? 'selected' : '' }}>published</option>
            </select>
        </div>
        <button type="submit">Update</button>
    </form>
</body>
</html>
