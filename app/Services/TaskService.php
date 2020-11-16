<?php

namespace App\Services;

use App\Exceptions\APIException;
use App\Repositories\TaskRepositoryInterface;
use App\Repositories\UserRepositoryInterface;


class TaskService
{

    private $taskRepository;
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TaskRepositoryInterface $taskRepository) {

        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;

    }


    public function create(Int $userId, ?Int $projectId, String $priority,
        String $title, String $description, String $scheduledDate, String $remeberDate)
    {
        throw Exception('Not implemented yet');
    }

    public function markAsDone(Int $userId, Int $taskId){
        throw Exception('Not implemented yet');
    }

    public function getDoneTasks(Int $userId){
        throw Exception('Not implemented yet');
    }

    public function edit(Int $userId, Int $taskId, array $data)
    {
        throw Exception('Not implemented yet');
    }

    public function delete(Int $userId, Int $taskId)
    {
        throw Exception('Not implemented yet');
    }
}
