<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Activities</title>
</head>
<body>
    <h1>Desa Activities</h1>

    @forelse ($activities as $activity)
        <div>{{ $activity->title }}</div>
    @empty
        <div>No activities found.</div>
    @endforelse
</body>
</html>
