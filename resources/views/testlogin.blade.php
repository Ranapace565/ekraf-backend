<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login with Google</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            margin-top: 100px;
        }

        a.google-btn {
            background: #db4437;
            color: #fff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        a.google-btn:hover {
            background: #c1351d;
        }
    </style>
</head>

<body>
    <h1>Login or Sign up with Google</h1>
    <a href="{{ route('google.redirect') }}" class="google-btn">
        Sign in with Google
    </a>
</body>

</html>
