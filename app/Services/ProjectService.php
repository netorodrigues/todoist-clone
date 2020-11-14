<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use App\Exceptions\APIException;

class ProjectService{

    public $projectRepository;

    public function __construct(UserRepositoryInterface $userRepository){
        $this->projectRepository = $userRepository;
    }

    public function create(Int $userId, String $name, String $color){
        return true;
    }

    public function edit(Int $userId, Int $projectId,  Array $data){
        return true;
    }

    public function delete(Int $userId, Int $projectId){
        return false;
    }
}
