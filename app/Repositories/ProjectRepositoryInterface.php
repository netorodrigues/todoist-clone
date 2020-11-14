<?php

namespace App\Repositories;

interface ProjectRepositoryInterface{

    public function create(
        Int $userId,
        String $name,
        String $color
    ) : Array;

    public function getById(Int $projectId) : Array;

    public function getByUser(Int $userId): Array;

    public function edit(
        Int $projectId,
        Array $data
    ): bool;

    public function delete(Int $projectId): bool;


}
