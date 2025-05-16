<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pengajuan Ditolak</title>
</head>

<body>
    <h2>Halo {{ $user->name }},</h2>
    <p>Mohon maaf, usahamu <strong> telah dinonaktifkan</strong>.</p>
    <p>Catatan dari admin: <em>{{ $note }}</em></p>
    <p>Silakan masuk ke website <a href="">Ekraf Nganjuk</a> untuk memperbarui dan mengajukan ulang.</p>
    <a href="{{ route('BusinessDisable') }}"
        style="display:inline-block; padding:10px 15px; background-color:#e74c3c; color:#fff; text-decoration:none; border-radius:5px;">Cek
        Usahamu Sekarang</a>
    <p>Terima kasih,<br>EKRAF NGANJUK</p>
</body>

</html>
