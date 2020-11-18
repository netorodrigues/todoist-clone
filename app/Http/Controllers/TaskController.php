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
        // $this->validate($request, [
        //     'name' => 'required|string',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|confirmed',
        // ]);

        return response()->json([], 200);
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
