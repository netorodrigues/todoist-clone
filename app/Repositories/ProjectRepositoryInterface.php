<?php

namespace App\Repositories;

interface ProjectRepositoryInterface
{

    public function create(
        Int $userId,
        String $name,
        String $color
    ): array;

    public function getById(Int $projectId): array;

    public function getByUser(Int $userId): array;

    public function edit(
        Int $projectId,
        array $data
    ): bool;

    public function delete(Int $projectId): bool;

}
