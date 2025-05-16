<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pengajuan Disetujui</title>
</head>

<body>
    <h2>Halo {{ $user->name }},</h2>
    <p>Selamat! Pengajuan usaha <strong>{{ $businessName }}</strong> telah disetujui dan telah masuk ke daftar usaha
        terverifikasi.</p>
    <p>Silakan login untuk melihat status usaha Anda.</p>
    <a href="{{ url('/login') }}"
        style="display:inline-block; padding:10px 15px; background-color:#3498db; color:#fff; text-decoration:none; border-radius:5px;">Login
        Sekarang</a>
    <p>Terima kasih,<br>EKRAF NGANJUK</p>
</body>

</html>
