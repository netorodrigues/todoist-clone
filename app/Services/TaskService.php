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

    private function userIsTaskOwner(String $userId, String $taskId)
    {
        $tasks = $this->taskRepository->getByUser($userId);

        foreach ($tasks as $task) {
            if ($task['id'] === $taskId) {
                return true;
            }
        }

        return false;

    }

    public function create(String $userId, ?String $projectId, String $priority,
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

    public function markAsDone(String $userId, String $taskId)
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

    public function get(String $userId)
    {
        return $this->taskRepository->getByUser($userId);
    }
    public function getDoneTasks(String $userId)
    {
        return $this->taskRepository->getDoneByUser($userId);
    }

    public function edit(String $userId, String $taskId, array $data)
    {
        $isOwner = $this->userIsTaskOwner($userId, $taskId);

        if (!$isOwner) {
            throw new APIException('User is not task owner', [
                'task_id' => $taskId,
                'user_id' => $userId,
            ]);
        }

        $projectId = $data['project_id'] ?? null;

        if ($projectId) {
            $isProjectOwner = $this->projectService->userIsProjectOwner($userId, $projectId);
            if (!$isProjectOwner) {
                throw new APIException("User is not project owner.",
                    ['user_id' => $userId, 'project_id' => $projectId]);
            }
        }

        $wasEdit = $this->taskRepository->edit($taskId, $data);

        if (!$wasEdit) {
            return false;
        }

        return $this->taskRepository->getById($taskId);
    }

    public function delete(String $userId, String $taskId)
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
