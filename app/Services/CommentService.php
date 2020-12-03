<?php

namespace App\Services;

use App\Exceptions\APIException;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\TaskRepositoryInterface;
use App\Repositories\UserRepositoryInterface;

class CommentService
{

    private $commentRepository;
    private $userRepository;
    private $projectRepository;
    private $taskRepository;

    public function __construct(
        CommentRepositoryInterface $commentRepository,
        UserRepositoryInterface $userRepository,
        ProjectRepositoryInterface $projectRepository,
        TaskRepositoryInterface $taskRepository) {

        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
    }

    private function userExists(String $userId)
    {
        $user = $this->userRepository->getById($userId);

        return !empty($user);
    }

    private function projectExists(String $projectId)
    {
        $project = $this->projectRepository->getById($projectId);

        return !empty($project);
    }

    private function taskExists(String $taskId)
    {
        $task = $this->taskRepository->getById($taskId);

        return !empty($task);
    }

    public function createForProject(String $userId, String $projectId, String $content)
    {
        if (!$this->userExists($userId)) {
            throw new APIException("Trying to create a comment for non-existing user", ['userId' => $userId]);
        }

        if (!$this->projectExists($projectId)) {
            throw new APIException("Trying to create a comment for non-existing project", ['projectId' => $projectId]);
        }

        $comment = $this->commentRepository->create($userId, $projectId, null, $content);

        return $comment;
    }

    public function createForTask(String $userId, String $taskId, String $content)
    {
        if (!$this->userExists($userId)) {
            throw new APIException("Trying to create a comment for non-existing user", ['userId' => $userId]);
        }

        if (!$this->taskExists($taskId)) {
            throw new APIException("Trying to create a comment for non-existing task", ['taskId' => $taskId]);
        }
        $comment = $this->commentRepository->create($userId, null, $taskId, $content);

        return $comment;
    }

    public function getForUser(String $userId)
    {
        return $this->commentRepository->getByUser($userId);
    }

    public function getForProject(String $projectId)
    {
        return $this->commentRepository->getByProject($projectId);
    }

    public function getForTask(String $taskId)
    {
        return $this->commentRepository->getByTask($taskId);
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
