<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function getById(Int $userId): array
    {
        $user = User::find($userId);

        return $user ? $user->toArray() : [];
    }

    public function getByEmail(String $email): array
    {
        $user = User::firstWhere('email', $email);

        return $user ? $user->toArray() : [];
    }

    public function create(String $name, String $preferName, String $email, String $password): array
    {

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->prefer_name = $preferName;
        $user->password = $password;
        $user->save();

        return $user->toArray();
    }

    public function edit(Int $userId, array $data): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $user->fill($data);
        $user->save();

        return true;
    }

    public function delete(Int $userId): bool
    {
        $user = User::find($userId);

        if ($user) {
            $user->delete();
        }

        return true;
    }
}
