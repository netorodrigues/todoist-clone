<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface{

    public function create(
        Int $userId,
        Int $projectId,
        String $priority,
        String $title,
        String $description,
        String $scheduledDate,
        String $rememberDate
    ) : Array{
        return [];
    }

    public function getById(Int $taskId) : Array{
        return [];
    }

    public function getByUser(Int $userId): Array{
        return [];
    }

    public function getByProject(Int $projectId): Array{
        return [];
    }

    public function markAsDone(Int $taskId): bool{
        return false;
    }

    public function edit(
        Int $taskId,
        Array $data
    ): bool{
        return false;
    }

    public function delete(Int $taskId): bool{
        return false;
    }


}
