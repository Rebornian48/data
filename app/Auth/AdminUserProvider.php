<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminUserProvider implements UserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        if ($identifier === '1') {
            return $this->createAdminUser();
        }
        return null;
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void {}

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $username = $credentials['username'] ?? '';
        $password = $credentials['password'] ?? '';

        if ($username === 'admin' && $password === 'data_jkt48') {
            return $this->createAdminUser();
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $credentials['username'] === 'admin' && $credentials['password'] === 'data_jkt48';
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void {}

    protected function createAdminUser(): AdminUser
    {
        return new AdminUser();
    }
}
