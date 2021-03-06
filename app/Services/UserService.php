<?php

namespace App\Services;

use App\Exceptions\APIException;
use App\Repositories\UserRepositoryInterface;

class UserService
{

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(String $name, String $email, String $password, String $preferName)
    {
        $existingUser = $this->userRepository->getByEmail($email);

        if (!empty($existingUser)) {
            throw new APIException("User with this email already exists", ['email' => $email]);
        }

        $hashedPassword = app('hash')->make($password);

        $user = $this->userRepository->create($name, $preferName, $email, $hashedPassword);
        return $user;
    }

    public function edit(String $userId, array $data)
    {
        $userExists = $this->userRepository->getById($userId);

        if (empty($userExists)) {
            throw new APIException("User with this id does not exists", ['id' => $userId]);
        }

        $wasEdited = $this->userRepository->edit($userId, $data);

        if ($wasEdited) {
            return $this->userRepository->getById($userId);
        }

        return false;
    }

    public function delete(String $userId)
    {
        return $this->userRepository->delete($userId);
    }
}
