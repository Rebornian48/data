<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function edit()
    {
        $user = $this->currentUser();

        return view('admin.password.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'username'              => ['required', 'string', 'max:64', Rule::unique('admin_users', 'username')->ignore($this->currentUserId())],
            'current_password'      => ['required', 'string'],
            'new_password'          => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $this->currentUser();

        if (! $user || ! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini salah.',
            ]);
        }

        $user->update([
            'username' => $data['username'],
            'password' => Hash::make($data['new_password']),
        ]);

        session([
            'admin_user_id'  => $user->id,
            'admin_username' => $user->username,
        ]);

        return redirect()
            ->route('admin.password.edit')
            ->with('success', 'Password berhasil diubah.');
    }

    private function currentUserId(): ?int
    {
        return session('admin_user_id') ?: null;
    }

    private function currentUser(): ?AdminUser
    {
        $id = $this->currentUserId();

        if ($id) {
            return AdminUser::find($id);
        }

        // Fallback: promote default hardcoded user into DB row on first change.
        $username = session('admin_username', 'admin');

        return AdminUser::firstOrCreate(
            ['username' => $username],
            ['password' => Hash::make('data_jkt48')]
        );
    }
}
