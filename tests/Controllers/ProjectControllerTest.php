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

    private function create_multiple_projects_for_user($projectsAmount)
    {
        for ($i = 1; $i <= $projectsAmount; $i++) {
            $this->post('/api/projects', [
                'name' => "project-number-{$i}",
                'color' => '#FFFFFF',
            ], [
                'Authorization' => "Bearer {$this->userToken}",
            ])->seeJson([
                'message' => 'CREATED',
            ]);
        }
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
    public function api_can_edit_projects()
    {
        $projectResponse = $this->post('/api/projects', [
            'name' => 'project-name',
            'color' => '#FFFFFF',
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $project = $projectResponse['project'];

        $this->put('/api/projects', [
            'project_id' => $project['id'],
            'color' => '#000000',
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'EDITED',
        ]);
    }

    /** @test */
    public function api_can_delete_projects()
    {
        $projectResponse = $this->post('/api/projects', [
            'name' => 'project-name',
            'color' => '#FFFFFF',
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $project = $projectResponse['project'];

        $this->delete('/api/projects', [
            'project_id' => $project['id'],
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'DELETED',
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

    /** @test */
    public function api_must_return_all_user_projects()
    {
        $projectAmount = 5;
        $this->create_multiple_projects_for_user($projectAmount);

        $getProjectsResponse = $this->get('/api/projects', [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $projectsReceived = $getProjectsResponse['projects'];
        $this->assertTrue(count($projectsReceived) === $projectAmount);
    }

}
