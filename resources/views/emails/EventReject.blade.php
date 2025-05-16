<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pengajuan Ditolak</title>
</head>

<body>
    <h2>Halo {{ $user->name }},</h2>
    <p>Mohon maaf, pengajuan event Anda yang berjudul <b>{{ $title }}</b> <strong>tidak disetujui</strong>.</p>
    <p>Catatan dari admin: <em>{{ $note }}</em></p>
    <p>Silakan login ke website EKRAF NGANJUK untuk memperbarui atau mengajukan ulang.</p>
    <a href="{{ route('SubmissionReject') }}"
        style="display:inline-block; padding:10px 15px; background-color:#e74c3c; color:#fff; text-decoration:none; border-radius:5px;">
        Ajukan Eventmu Lagi
    </a>
    <p>Terima kasih,<br>EKRAF NGANJUK</p>
</body>

</html>
