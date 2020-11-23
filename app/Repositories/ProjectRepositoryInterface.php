<?php

namespace App\Repositories;

interface ProjectRepositoryInterface
{

    public function create(
        String $userId,
        String $name,
        String $color
    ): array;

    public function getById(String $projectId): array;

    public function getByUser(String $userId): array;

    public function edit(
        String $projectId,
        array $data
    ): bool;

    public function delete(String $projectId): bool;

}
