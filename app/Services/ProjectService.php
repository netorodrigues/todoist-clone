<?php

namespace App\Services;

use App\Exceptions\APIException;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\UserRepositoryInterface;

class ProjectService
{

    private $projectRepository;
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ProjectRepositoryInterface $projectRepository) {

        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;

    }

    private function isHexColor($hex)
    {
        $hexWithoutHashtag = explode('#', $hex);

        if (count($hexWithoutHashtag) !== 2) {
            return false;
        }

        return ctype_xdigit($hexWithoutHashtag[1]);
    }

    public function userIsProjectOwner($userId, $projectId)
    {
        $userProjects = $this->projectRepository->getByUser($userId);

        foreach ($userProjects as $userProject) {
            if ($userProject['id'] == $projectId) {
                return true;
            }
        }

        return false;
    }

    public function create(String $userId, String $name, String $color)
    {
        $user = $this->userRepository->getById($userId);

        if (empty($user)) {
            throw new APIException("Trying to create project for non-existing user", ['userId' => $userId]);
        }

        if (!$this->isHexColor($color)) {
            throw new APIException("Trying to create project with invalid color", ['color' => $color]);
        }

        return $this->projectRepository->create($userId, $name, $color);
    }

    public function get(String $userId)
    {

        $user = $this->userRepository->getById($userId);

        if (empty($user)) {
            throw new APIException("Trying to get projects for non-existing user", ['userId' => $userId]);
        }

        return $this->projectRepository->getByUser($userId);
    }

    public function edit(String $userId, String $projectId, array $data)
    {
        $user = $this->userRepository->getById($userId);

        if (empty($user)) {
            throw new APIException("Trying to edit project for non-existing user", ['userId' => $userId]);
        }

        $project = $this->projectRepository->getById($projectId);

        if (empty($project)) {
            throw new APIException("Trying to edit non-existing project", ['projectId' => $projectId]);
        }

        $userHaveProject = $this->userIsProjectOwner($userId, $projectId);

        if (!$userHaveProject) {
            throw new APIException("Trying to edit project of other user", ['userId' => $userId, 'projectId' => $projectId]);
        }

        $wasEdited = $this->projectRepository->edit($projectId, $data);

        if ($wasEdited) {
            return $this->projectRepository->getById($projectId);
        }

        return false;
    }

    public function delete(String $userId, String $projectId)
    {
        $user = $this->userRepository->getById($userId);

        if (empty($user)) {
            throw new APIException("Trying to delete project for non-existing user", ['userId' => $userId]);
        }

        $project = $this->projectRepository->getById($projectId);

        if (empty($project)) {
            throw new APIException("Trying to delete non-existing project", ['projectId' => $projectId]);
        }

        $userHaveProject = $this->userIsProjectOwner($userId, $projectId);

        if (!$userHaveProject) {
            throw new APIException("Trying to delete project of other user", ['userId' => $userId, 'projectId' => $projectId]);
        }

        return $this->projectRepository->delete($project['id']);

    }
}
