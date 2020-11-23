<?php

namespace App\Repositories;

interface TaskRepositoryInterface
{

    public function create(
        String $userId,
        ?String $projectId,
        String $priority,
        String $title,
        String $description,
        ?String $scheduledDate,
        ?String $rememberDate
    ): array;

    public function getById(String $taskId): array;

    public function getByUser(String $userId): array;

    public function getByProject(String $projectId): array;

    public function markAsDone(String $taskId): bool;

    public function getDoneByUser(String $userId): array;

    public function edit(
        String $taskId,
        array $data
    ): bool;

    public function delete(String $taskId): bool;

}
