<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JKT48 Database Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-brand { background: linear-gradient(135deg, #E60012 0%, #ff4d6d 100%); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 gradient-brand rounded-2xl flex items-center justify-center text-white font-black text-2xl mx-auto mb-4">
                48
            </div>
            <h1 class="text-2xl font-bold text-slate-900">JKT48 Database</h1>
            <p class="text-slate-500 text-sm mt-1">Admin Panel Login</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-5">
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition text-sm">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition text-sm">
                </div>

                <button type="submit"
                        class="w-full gradient-brand text-white font-semibold py-2.5 rounded-lg hover:opacity-90 transition text-sm">
                    Login
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 hover:text-slate-700">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
