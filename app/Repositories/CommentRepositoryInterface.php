<?php

namespace App\Repositories;

interface CommentRepositoryInterface
{

    public function create(
        String $userId,
        ?String $projectId,
        ?String $taskId,
        String $content
    ): array;

    public function getById(String $commentId): array;

    public function getByProject(String $projectId): array;

    public function getByTask(String $taskId): array;

    public function getByUser(String $userId): array;

    public function edit(
        String $commentId,
        array $data
    ): bool;

    public function delete(String $commentId): bool;

}
