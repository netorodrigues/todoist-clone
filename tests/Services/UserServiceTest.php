<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Services\UserService;
use App\Exceptions\APIException;

class UserServiceTest extends TestCase
{
    use DatabaseTransactions;
    private $userService;
    private $invalidId = -1;

    public function setUp(): void{
        parent::setUp();
        $this->userService = app(UserService::class);
    }

    /** @test */
    public function can_create_user()
    {
        $response = $this->userService->create('name', 'email', 'password', 'prefer_name');
        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('id',$response));
    }

    /** @test */
    public function cannot_create_user_with_same_email()
    {
        $response = $this->userService->create('name', 'email', 'password', 'prefer_name');
        $this->assertTrue(is_array($response));

        $this->expectException(APIException::class);
        $this->userService->create('name', 'email', 'password', 'prefer_name');
    }

    /** @test */
    public function can_edit_existing_user()
    {
        $user = $this->userService->create('name', 'email', 'password', 'prefer_name');
        $this->assertTrue(is_array($user));

        $editedUser = $this->userService->edit($user['id'], ['email' => 'new-email']);
        $this->assertTrue(is_array($editedUser));
        $this->assertTrue($editedUser['email'] === 'new-email');
    }

    /** @test */
    public function cannot_edit_non_existing_user()
    {
        $this->expectException(APIException::class);
        $editedUser = $this->userService->edit($this->invalidId, ['email' => 'new-email']);
    }

    /** @test */
    public function can_delete_user()
    {
        $user = $this->userService->create('name', 'email', 'password', 'prefer_name');
        $this->assertTrue(is_array($user));

        $wasDeleted = $this->userService->delete($user['id']);
        $this->assertTrue($wasDeleted === true);
    }

    /** @test */
    public function receive_same_response_trying_to_delete_non_existing_user()
    {
        $wasDeleted = $this->userService->delete($this->invalidId);
        $this->assertTrue($wasDeleted === true);
    }




}
