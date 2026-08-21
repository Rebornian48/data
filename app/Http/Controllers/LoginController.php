<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    private string $username = 'admin';
    private string $password = 'data_jkt48';

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

        if ($request->input('username') === $this->username && $request->input('password') === $this->password) {
            session(['admin_logged_in' => true]);
            $request->session()->regenerate();
            return redirect()->intended(route('admin.home'));
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        session()->forget('admin_logged_in');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
