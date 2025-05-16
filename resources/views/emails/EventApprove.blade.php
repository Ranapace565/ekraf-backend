<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pengajuan Disetujui</title>
</head>

<body>
    <h2>Halo {{ $user->name }},</h2>
    <p>Selamat! Pengajuan event berjudul <strong>{{ $title }}</strong> telah disetujui dan telah
        di publikasikan.</p>
    <p>Silakan login untuk melihat event Anda.</p>
    <a href="{{ route('Event') }}"
        style="display:inline-block; padding:10px 15px; background-color:#3498db; color:#fff; text-decoration:none; border-radius:5px;">
        Lengkapi Data Usahamu Sekarang
    </a>
    <p>Terima kasih,<br>EKRAF NGANJUK</p>
</body>

</html>
