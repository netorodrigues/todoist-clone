<?php

namespace App\Services;

use App\Repositories\CommentRepositoryInterface;

class CommentService
{

    private $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function createForProject(String $userId, String $projectId, String $content)
    {
        $comment = $this->commentRepository->create($userId, $projectId, null, $content);

        return $comment;
    }

    public function createForTask(String $userId, String $taskId, String $content)
    {
        $comment = $this->commentRepository->create($userId, null, $taskId, $content);

        return $comment;
    }

    public function getForUser(String $userId)
    {
        return false;
    }

    public function getForProject(String $projectId)
    {
        return false;
    }

    public function getForTask(String $taskId)
    {
        return false;
    }

    public function edit(String $userId, String $commentId, array $data)
    {
        return false;
    }

    public function delete(String $userId, String $commentId)
    {
        return false;
    }
}
