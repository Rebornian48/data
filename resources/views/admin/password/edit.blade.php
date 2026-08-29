@extends('layouts.admin')

@section('page_title', 'Ubah Password')

@section('content')
<form method="POST" action="{{ route('admin.password.update') }}">
    @csrf
    @method('PUT')

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4 max-w-xl">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Username *</label>
            <input type="text" name="username" required
                   value="{{ old('username', $user->username ?? session('admin_username', 'admin')) }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password Saat Ini *</label>
            <input type="password" name="current_password" required autocomplete="current-password"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru *</label>
                <input type="password" name="new_password" required minlength="8" autocomplete="new-password"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                <p class="text-xs text-slate-500 mt-1">Minimum 8 karakter.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru *</label>
                <input type="password" name="new_password_confirmation" required minlength="8" autocomplete="new-password"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="bg-brand text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Simpan Perubahan</button>
        <a href="{{ route('admin.home') }}" class="px-6 py-2 text-slate-600 hover:text-slate-900">Batal</a>
    </div>
</form>
@endsection
