<?php

namespace App\Services;

use App\Exceptions\APIException;
use App\Repositories\TaskRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Services\ProjectService;

class TaskService
{

    private $taskRepository;
    private $userRepository;

    private $projectService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TaskRepositoryInterface $taskRepository,
        ProjectService $projectService) {

        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;

        $this->projectService = $projectService;

    }

    private function userIsTaskOwner($userId, $taskId)
    {
        $tasks = $this->taskRepository->getByUser($userId);

        foreach ($tasks as $task) {
            if ($task['id'] === $taskId) {
                return true;
            }
        }

        return false;

    }

    public function create(Int $userId, ?Int $projectId, String $priority,
        String $title, String $description, ?String $scheduledDate, ?String $rememberDate) {
        $existingUser = $this->userRepository->getById($userId);

        if (empty($existingUser)) {
            throw new APIException("User not found.", ['user_id' => $userId]);
        }

        if ($projectId) {
            $isProjectOwner = $this->projectService->userIsProjectOwner($userId, $projectId);
            if (!$isProjectOwner) {
                throw new APIException("User is not project owner.",
                    ['user_id' => $userId, 'project_id' => $projectId]);
            }
        }

        return $this->taskRepository->create(
            $userId, $projectId, $priority, $title,
            $description, $scheduledDate, $rememberDate
        );
    }

    public function markAsDone(Int $userId, Int $taskId)
    {

        $isOwner = $this->userIsTaskOwner($userId, $taskId);

        if (!$isOwner) {
            throw new APIException('User is not task owner', [
                'task_id' => $taskId,
                'user_id' => $userId,
            ]);
        }

        return $this->taskRepository->markAsDone($taskId);
    }

    public function get(Int $userId)
    {
        return $this->taskRepository->getByUser($userId);
    }
    public function getDoneTasks(Int $userId)
    {
        return $this->taskRepository->getDoneByUser($userId);
    }

    public function edit(Int $userId, Int $taskId, array $data)
    {
        $isOwner = $this->userIsTaskOwner($userId, $taskId);

        if (!$isOwner) {
            throw new APIException('User is not task owner', [
                'task_id' => $taskId,
                'user_id' => $userId,
            ]);
        }

        $wasEdit = $this->taskRepository->edit($taskId, $data);

        if (!$wasEdit) {
            return false;
        }

        return $this->taskRepository->getById($taskId);
    }

    public function delete(Int $userId, Int $taskId)
    {
        $isOwner = $this->userIsTaskOwner($userId, $taskId);

        if (!$isOwner) {
            throw new APIException('User is not task owner', [
                'task_id' => $taskId,
                'user_id' => $userId,
            ]);
        }

        return $this->taskRepository->delete($taskId);
    }
}
