<?php

use App\Services\UserService;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProjectControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $userService;
    private $userInstance;
    private $userToken;

    public function setUp(): void
    {
        parent::setUp();

        $this->userService = app(UserService::class);
        $this->userInstance = $this->userService->create(
            'name', 'email@gmail.com', 'password', 'prefer_name'
        );

        $sessionResponse = $this->post('/api/users/login', [
            'email' => 'email@gmail.com',
            'password' => 'password',
        ])->response;

        $this->userToken = $sessionResponse['token'];
    }

    /** @test */
    public function api_can_create_project()
    {
        $this->post('/api/projects', [
            'name' => 'project-name',
            'color' => '#FFFFFF',
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'CREATED',
        ]);
    }

    /** @test */
    public function api_must_refuse_invalid_token_requests()
    {
        $this->post('/api/projects', [
            'name' => 'project-name',
            'color' => '#FFFFFF',
        ], [
            'Authorization' => "Bearer invalid-token",
        ])->seeJson([
            'status' => 'Authorization Token not defined or invalid',
        ]);
    }
}
