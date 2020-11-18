<?php

namespace App\Repositories;

interface TaskRepositoryInterface
{

    public function create(
        Int $userId,
        Int $projectId,
        String $priority,
        String $title,
        String $description,
        String $scheduledDate,
        String $rememberDate
    ): array;

    public function getById(Int $taskId): array;

    public function getByUser(Int $userId): array;

    public function getByProject(Int $projectId): array;

    public function markAsDone(Int $taskId): bool;

    public function getDoneByUser(Int $userId): array;

    public function edit(
        Int $taskId,
        array $data
    ): bool;

    public function delete(Int $taskId): bool;

}
