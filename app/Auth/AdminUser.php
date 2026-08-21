<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class AdminUser implements Authenticatable
{
    private string $id = '1';
    private string $username = 'admin';
    private string $password;

    public function __construct()
    {
        $this->password = env('ADMIN_PASSWORD_HASH', Hash::make('data_jkt48'));
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void {}

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
