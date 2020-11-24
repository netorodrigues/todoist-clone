<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private $taskService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    public function create(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'title' => 'required|string',
            'priority' => 'required|integer',
        ]);

        $user = auth('api')->user();

        $requestData = $request->only([
            'title', 'description', 'project_id',
            'scheduled_date', 'remember_date', 'priority',
        ]);

        try {
            $task = $this->taskService->create(
                $user->id,
                $requestData['project_id'] ?? null,
                $requestData['priority'],
                $requestData['title'],
                $requestData['description'],
                $requestData['scheduled_date'] ?? null,
                $requestData['remember_date'] ?? null,
            );

            return response()->json(['message' => 'CREATED', 'task' => $task], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create task for user'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function get()
    {
        $user = auth('api')->user();

        try {
            $tasks = $this->taskService->get($user->id);
            return response()->json(['tasks' => $tasks], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to get tasks for user'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function edit(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'task_id' => 'required|string',
        ]);

        $user = auth('api')->user();

        $requestData = $request->except(['task_id']);
        $taskId = $request->input('task_id');
        try {
            $task = $this->taskService->edit(
                $user->id,
                $taskId,
                $requestData
            );

            return response()->json(['message' => 'EDITED', 'task' => $task], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to edit task for user'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'task_id' => 'required|string',
        ]);

        $user = auth('api')->user();
        $taskId = $request->input('task_id');

        try {
            $task = $this->taskService->delete(
                $user->id,
                $taskId,
            );

            return response()->json(['message' => 'DELETED'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete task for user'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function markDone(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'task_id' => 'required|string',
        ]);

        $user = auth('api')->user();
        $taskId = $request->input('task_id');

        try {
            $task = $this->taskService->markAsDone(
                $user->id,
                $taskId,
            );

            return response()->json(['message' => 'DONE'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete task for user'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getDone(Request $request)
    {
        $user = auth('api')->user();

        try {
            $tasks = $this->taskService->getDoneTasks(
                $user->id,
            );

            return response()->json(['tasks' => $tasks, 'message' => 'DONE'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete task for user'], Response::HTTP_BAD_REQUEST);
        }
    }

}
