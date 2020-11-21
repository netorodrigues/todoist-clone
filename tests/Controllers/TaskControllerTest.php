<?php

use App\Services\ProjectService;
use App\Services\UserService;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $userService;
    private $projectService;

    private $userInstance;
    private $projectInstance;

    private $userToken;

    private $dateExample;

    public function setUp(): void
    {
        parent::setUp();

        $this->userService = app(UserService::class);
        $this->projectService = app(ProjectService::class);

        $this->userInstance = $this->userService->create(
            'name', 'email@gmail.com', 'password', 'prefer_name'
        );

        $this->projectInstance = $this->projectService->create(
            $this->userInstance['id'], 'project-name', '#FFFFFF'
        );

        $sessionResponse = $this->post('/api/users/login', [
            'email' => 'email@gmail.com',
            'password' => 'password',
        ])->response;

        $this->userToken = $sessionResponse['token'];
        $this->dateExample = date("Y-m-d H:i:s", strtotime('now'));
    }

    /** @test */
    public function api_can_create_task()
    {
        $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
            'remember_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'CREATED',
        ]);
    }

    /** @test */
    public function api_can_create_task_without_project()
    {
        $this->post('/api/tasks', [
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
            'remember_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'CREATED',
        ]);
    }

    /** @test */
    public function api_considers_dates_optional_to_create_task()
    {
        $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'CREATED',
        ]);

        $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'remember_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'CREATED',
        ]);

        $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'CREATED',
        ]);
    }

}
