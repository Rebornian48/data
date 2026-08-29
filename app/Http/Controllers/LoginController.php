<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // Fallback saat tabel admin_users belum diseed.
    private const FALLBACK_USER = 'admin';
    private const FALLBACK_PASS = 'data_jkt48';

    public function showLoginForm()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $username = (string) $request->input('username');
        $password = (string) $request->input('password');

        if ($this->attempt($username, $password, $adminId)) {
            session([
                'admin_logged_in' => true,
                'admin_user_id'   => $adminId,
                'admin_username'  => $username,
            ]);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.home'));
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        session()->forget(['admin_logged_in', 'admin_user_id', 'admin_username']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function attempt(string $username, string $password, ?int &$adminId): bool
    {
        try {
            $user = AdminUser::where('username', $username)->first();
        } catch (\Throwable $e) {
            $user = null;
        }

        if ($user && Hash::check($password, $user->password)) {
            $adminId = $user->id;
            return true;
        }

        // Fallback jika tabel admin_users belum ada / kosong.
        $noUsers = false;
        try {
            $noUsers = AdminUser::count() === 0;
        } catch (\Throwable $e) {
            $noUsers = true;
        }

        if ($noUsers && $username === self::FALLBACK_USER && $password === self::FALLBACK_PASS) {
            $adminId = 0;
            return true;
        }

        return false;
    }
}
