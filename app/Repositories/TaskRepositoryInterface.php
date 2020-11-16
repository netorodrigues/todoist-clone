<?php

namespace App\Repositories;

interface TaskRepositoryInterface{

    public function create(
        Int $userId,
        Int $projectId,
        String $priority,
        String $title,
        String $description,
        String $scheduledDate,
        String $rememberDate
    ) : Array;

    public function getById(Int $taskId) : Array;

    public function getByUser(Int $userId): Array;

    public function getByProject(Int $projectId): Array;

    public function markAsDone(Int $taskId): bool;

    public function edit(
        Int $taskId,
        Array $data
    ): bool;

    public function delete(Int $taskId): bool;


}
