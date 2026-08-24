<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JKT48 Database Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/neo.css') }}?v=1">
</head>
<body class="neo-body min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 flex items-center justify-center text-black font-black text-2xl mx-auto mb-4" style="background:#ff6b9d;border:3px solid #000;box-shadow:5px 5px 0 #000;">
                48
            </div>
            <h1 class="display text-3xl">JKT48 DATABASE</h1>
            <p class="text-sm font-bold mt-1">Admin Panel Login</p>
        </div>

        <div class="p-8" style="background:#fff;border:3px solid #000;box-shadow:6px 6px 0 #000;">
            @if ($errors->any())
                <div class="mb-6 px-4 py-3 text-sm font-bold" style="background:#ef4444;border:3px solid #000;color:#000;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-5">
                    <label for="username" class="block text-sm font-bold mb-2">USERNAME</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                           class="w-full text-sm">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-bold mb-2">PASSWORD</label>
                    <input type="password" name="password" id="password" required
                           class="w-full text-sm">
                </div>

                <button type="submit"
                        class="w-full font-bold py-3 text-sm" style="background:#ff6b9d;color:#000;">
                    LOGIN
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('dashboard') }}" class="text-sm font-bold hover:underline">
                &larr; KEMBALI KE DASHBOARD
            </a>
        </div>
    </div>
</body>
</html>
