<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            return response()->json(['project' => $project, 'message' => 'CREATED'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Project creation failed!'], Response::HTTP_BAD_REQUEST);
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
            return response()->json(['projects' => $projects], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to get user projects'], Response::HTTP_BAD_REQUEST);
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
            return response()->json(['project' => $project, 'message' => 'EDITED'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to edit user project'], Response::HTTP_BAD_REQUEST);
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
            return response()->json(['message' => 'DELETED'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user project'], Response::HTTP_BAD_REQUEST);
        }
    }

}
