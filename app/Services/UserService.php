<?php

namespace App\Services;

use App\Models\User;

class UserService{

    public function create(String $name, String $email, String $password){

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = app('hash')->make($password);

        $user->save();

        return $user;

    }
}
