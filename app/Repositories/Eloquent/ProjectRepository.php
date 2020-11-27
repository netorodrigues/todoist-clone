<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;

final class ProjectRepository implements ProjectRepositoryInterface
{

    public function getById(String $projectId): array
    {
        $project = Project::find($projectId);

        return $project ? $project->toArray() : [];
    }

    public function getByUser(String $userId): array
    {
        $projects = Project::where('user_id', $userId)->get();
        return $projects->toArray();
    }

    public function create(String $userId, String $name, String $color): array
    {
        $project = new Project;
        $project->user_id = $userId;
        $project->name = $name;
        $project->color = $color;
        $project->save();

        return $project->toArray();
    }

    public function edit(String $projectId, array $data): bool
    {
        $project = Project::find($projectId);
        if (!$project) {
            return false;
        }

        $project->fill($data);
        $project->save();

        return true;
    }

    public function delete(String $projectId): bool
    {
        $project = Project::find($projectId);
        if ($project) {
            $project->delete();
        }

        return true;
    }
}
