<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kecamatan Activities</title>
</head>
<body>
    <h1>Kecamatan Activities</h1>

    @forelse ($activities as $activity)
        <div>{{ $activity->title }}</div>
    @empty
        <div>No activities found.</div>
    @endforelse
</body>
</html>
