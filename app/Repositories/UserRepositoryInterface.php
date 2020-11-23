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

    public function getById(String $userId): array;

    public function getByEmail(String $email): array;

    public function edit(
        String $userId,
        array $data
    ): bool;

    public function delete(String $userId): bool;

}
