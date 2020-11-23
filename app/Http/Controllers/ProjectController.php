<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    private $projectService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    public function create(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'name' => 'required|string',
            'color' => 'required|string',
        ]);

        $user = auth('api')->user();

        $projectName = $request->input('name');
        $projectColor = $request->input('color');

        try {
            $project = $this->projectService->create($user->id, $projectName, $projectColor);
            return response()->json(['project' => $project, 'message' => 'CREATED'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Project creation failed!'], 409);
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
            $projects = $this->projectService->get($user->id);
            return response()->json(['projects' => $projects], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to get user projects'], 409);
        }
    }

    public function edit(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'project_id' => 'required|string',
        ]);

        $user = auth('api')->user();
        $projectId = $request->input('project_id');

        try {
            $project = $this->projectService->edit($user->id, $projectId, $request->except(['project_id']));
            return response()->json(['project' => $project, 'message' => 'EDITED'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to edit user project'], 409);
        }

    }

    public function delete(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'project_id' => 'required|string',
        ]);

        $user = auth('api')->user();
        $projectId = $request->input('project_id');

        try {
            $project = $this->projectService->delete($user->id, $projectId);
            return response()->json(['message' => 'DELETED'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user project'], 409);
        }
    }

}
