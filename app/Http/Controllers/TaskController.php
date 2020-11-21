<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
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

            return response()->json(['message' => 'CREATED', 'task' => $task], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create task for user'], 409);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function get()
    {
        return response()->json([], 200);
    }

    public function edit()
    {
        return response()->json([], 200);
    }

    public function delete()
    {
        return response()->json([], 200);
    }

}
