<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{

    public function getById(Int $userId): array
    {
        return [];
    }

    public function getByUser(Int $userId): array
    {
        return [];
    }

    public function create(String $userId, String $name, String $color): array
    {
        return [];
    }

    public function edit(Int $userId, array $data): bool
    {
        return false;
    }

    public function delete(Int $userId):bool{
        return false;
    }
}
