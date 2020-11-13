<?php

namespace App\Repositories;

interface UserRepositoryInterface{

    public function create(
        String $name,
        String $preferName,
        String $email,
        String $password
    ) : Array;

    public function getById(Int $userId) : Array;

    public function getByEmail(String $email): Array;

    public function edit(
        Int $userId,
        Array $data
    ): bool;

    public function delete(Int $userId): bool;


}
