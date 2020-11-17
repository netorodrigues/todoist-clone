<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TaskRepositoryInterface;
use App\Models\Task;

class TaskRepository implements TaskRepositoryInterface{

    public function create(
        Int $userId,
        ?Int $projectId,
        String $priority,
        String $title,
        String $description,
        String $scheduledDate,
        String $rememberDate
    ) : Array{

        $task = new Task;
        $task->user_id = $userId;
        $task->project_id = $projectId;
        $task->priority = $priority;
        $task->title = $title;
        $task->description = $description;
        $task->scheduled_date = $scheduledDate;
        $task->remember_date = $rememberDate;
        $task->is_done = false;

        $task->save();

        return $task->toArray();
    }

    public function getById(Int $taskId) : Array{
        $task = Task::find($taskId);
        return $task ? $task->toArray() : [];
    }

    public function getByUser(Int $userId): Array{
        $tasks = Task::where('user_id', $userId)->get();
        return $tasks->toArray();
    }

    public function getByProject(Int $projectId): Array{
        $tasks = Task::where('project_id', $projectId)->get();
        return $tasks->toArray();
    }


    public function getDoneByUser(Int $userId): Array{

        $tasks = Task::where('user_id', $userId)
                 ->where('is_done', true)->get();
        return $tasks->toArray();
    }

    public function markAsDone(Int $taskId): bool{

        $task = Task::find($taskId);
        if (!$task) {
            return false;
        }

        $task->is_done = true;
        $task->save();

        return true;
    }

    public function edit(
        Int $taskId,
        Array $data
    ): bool{
        $task = Task::find($taskId);

        if (!$task) {
            return false;
        }

        $task->fill($data);
        $task->save();

        return true;
    }

    public function delete(Int $taskId): bool{
        $task = Task::find($taskId);

        if ($task){
            $task->delete();
        }

        return true;
    }


}
