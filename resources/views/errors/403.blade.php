<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
</head>
<body>
    <h1>403 - Akses Ditolak</h1>
    <p>{{ $message ?? 'Anda tidak memiliki izin untuk mengakses resource ini.' }}</p>
    <a href="{{ url('/dashboard') }}">Kembali ke Dashboard</a>
</body>
</html>
