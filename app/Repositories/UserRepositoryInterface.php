<?php

namespace App\Repositories;

interface UserRepositoryInterface
{

    public function create(
        String $name,
        String $preferName,
        String $email,
        String $password
    ): array;

    public function getById(Int $userId): array;

    public function getByEmail(String $email): array;

    public function edit(
        Int $userId,
        array $data
    ): bool;

    public function delete(Int $userId): bool;

}
