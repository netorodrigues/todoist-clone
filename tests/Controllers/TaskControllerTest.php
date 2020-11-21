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
    public function api_cannot_create_task_with_invalid_token()
    {
        $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
            'remember_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer invalid-token",
        ])->seeJson([
            'status' => 'Authorization Token not defined or invalid',
        ]);
    }

    /** @test */
    public function api_cannot_create_task_with_invalid_project()
    {
        $this->post('/api/tasks', [
            'project_id' => -1,
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
            'remember_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'Failed to create task for user',
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

    /** @test */
    public function api_can_edit_created_task()
    {
        $taskResponse = $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $task = $taskResponse['task'];

        $this->put('/api/tasks', [
            'task_id' => $task['id'],
            'priority' => 2,
            'title' => 'new-task-title',
            'description' => 'new-task-description',
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'EDITED',
            'title' => 'new-task-title',
            'description' => 'new-task-description',
        ]);
    }

    /** @test */
    public function api_cannot_edit_another_user_task()
    {

        $taskResponse = $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $task = $taskResponse['task'];

        $this->userService->create(
            'another-name', 'another-email@gmail.com',
            'another-password', 'prefer_name'
        );

        $sessionResponse = $this->post('/api/users/login', [
            'email' => 'another-email@gmail.com',
            'password' => 'another-password',
        ])->response;

        $anotherUserToken = $sessionResponse['token'];

        $this->put('/api/tasks', [
            'task_id' => $task['id'],
            'priority' => 2,
            'title' => 'new-task-title',
            'description' => 'new-task-description',
        ], [
            'Authorization' => "Bearer {$anotherUserToken}",
        ])->seeJson([
            'message' => 'Failed to edit task for user',
        ]);
    }

    /** @test */
    public function api_cannot_put_task_in_another_user_project()
    {

        $this->userService->create(
            'another-name', 'another-email@gmail.com',
            'another-password', 'prefer_name'
        );

        $sessionResponse = $this->post('/api/users/login', [
            'email' => 'another-email@gmail.com',
            'password' => 'another-password',
        ])->response;

        $anotherUserToken = $sessionResponse['token'];

        $taskResponse = $this->post('/api/tasks', [
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$anotherUserToken}",
        ])->response;

        $task = $taskResponse['task'];

        $this->put('/api/tasks', [
            'task_id' => $task['id'],
            'project_id' => $this->projectInstance['id'],
        ], [
            'Authorization' => "Bearer {$anotherUserToken}",
        ])->seeJson([
            'message' => 'Failed to edit task for user',
        ]);
    }

    /** @test */
    public function api_can_change_task_project()
    {
        $anotherProject = $this->projectService->create(
            $this->userInstance['id'], 'new-project-name', '#FFFFFF'
        );

        $taskResponse = $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $task = $taskResponse['task'];

        $this->put('/api/tasks', [
            'task_id' => $task['id'],
            'project_id' => $anotherProject['id'],
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'EDITED',
            'project_id' => $anotherProject['id'],
        ]);
    }

    /** @test */
    public function api_can_delete_task()
    {
        $taskResponse = $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $task = $taskResponse['task'];

        $this->delete('/api/tasks', [
            'task_id' => $task['id'],
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'DELETED',
        ]);
    }

    /** @test */
    public function api_cannot_delete_other_user_task()
    {
        $taskResponse = $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $task = $taskResponse['task'];

        $this->userService->create(
            'another-name', 'another-email@gmail.com',
            'another-password', 'prefer_name'
        );

        $sessionResponse = $this->post('/api/users/login', [
            'email' => 'another-email@gmail.com',
            'password' => 'another-password',
        ])->response;

        $anotherUserToken = $sessionResponse['token'];

        $this->delete('/api/tasks', [
            'task_id' => $task['id'],
        ], [
            'Authorization' => "Bearer {$anotherUserToken}",
        ])->seeJson([
            'message' => 'Failed to delete task for user',
        ]);
    }

    /** @test */
    public function api_can_get_tasks_for_user()
    {
        $taskAmount = 5;

        for ($i = 1; $i <= $taskAmount; $i++) {
            $this->post('/api/tasks', [
                'project_id' => $this->projectInstance['id'],
                'priority' => 1,
                'title' => "task-title {$i}",
                'description' => 'task-description',
                'scheduled_date' => $this->dateExample,
            ], [
                'Authorization' => "Bearer {$this->userToken}",
            ]);
        }

        $taskResponse = $this->get('/api/tasks', [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $tasks = $taskResponse['tasks'];
        $this->assertTrue(count($tasks) === $taskAmount);
    }

    /** @test */
    public function api_cannot_get_tasks_for_invalid_user()
    {
        $taskResponse = $this->get('/api/tasks', [
            'Authorization' => "Bearer invalid-token",
        ])->seeJson([
            'status' => 'Authorization Token not defined or invalid',
        ]);
    }

    /** @test */
    public function api_can_mark_task_as_done()
    {
        $taskResponse = $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $task = $taskResponse['task'];

        $this->post('/api/tasks/done', [
            'task_id' => $task['id'],
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->seeJson([
            'message' => 'DONE',
        ]);
    }

    /** @test */
    public function api_cannot_mark_another_user_task_as_done()
    {
        $taskResponse = $this->post('/api/tasks', [
            'project_id' => $this->projectInstance['id'],
            'priority' => 1,
            'title' => 'task-title',
            'description' => 'task-description',
            'scheduled_date' => $this->dateExample,
        ], [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $task = $taskResponse['task'];

        $this->userService->create(
            'another-name', 'another-email@gmail.com',
            'another-password', 'prefer_name'
        );

        $sessionResponse = $this->post('/api/users/login', [
            'email' => 'another-email@gmail.com',
            'password' => 'another-password',
        ])->response;

        $anotherUserToken = $sessionResponse['token'];

        $this->post('/api/tasks/done', [
            'task_id' => $task['id'],
        ], [
            'Authorization' => "Bearer {$anotherUserToken}",
        ])->seeJson([
            'message' => 'Failed to delete task for user',
        ]);
    }

    /** @test */
    public function api_can_get_done_tasks_for_user()
    {
        $taskAmount = 5;

        for ($i = 1; $i <= $taskAmount; $i++) {
            $taskResponse = $this->post('/api/tasks', [
                'project_id' => $this->projectInstance['id'],
                'priority' => 1,
                'title' => "task-title {$i}",
                'description' => 'task-description',
                'scheduled_date' => $this->dateExample,
            ], [
                'Authorization' => "Bearer {$this->userToken}",
            ])->response;

            $task = $taskResponse['task'];

            $this->post('/api/tasks/done', [
                'task_id' => $task['id'],
            ], [
                'Authorization' => "Bearer {$this->userToken}",
            ]);
        }

        $taskResponse = $this->get('/api/tasks/done', [
            'Authorization' => "Bearer {$this->userToken}",
        ])->response;

        $tasks = $taskResponse['tasks'];
        $this->assertTrue(count($tasks) === $taskAmount);
    }

    /** @test */
    public function api_cannot_get_done_tasks_for_invalid_user()
    {
        $taskResponse = $this->get('/api/tasks/done', [
            'Authorization' => "Bearer invalid-token",
        ])->seeJson([
            'status' => 'Authorization Token not defined or invalid',
        ]);
    }

}
