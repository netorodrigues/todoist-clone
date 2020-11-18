<?php

use App\Exceptions\APIException;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Services\UserService;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TaskServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $taskService;
    private $userService;
    private $projectService;

    private $invalidId = -1;

    public function setUp(): void
    {
        parent::setUp();
        $this->taskService = app(TaskService::class);
        $this->userService = app(UserService::class);
        $this->projectService = app(ProjectService::class);

        $this->userInstance = $this->userService->create(
            'name', 'email', 'password', 'prefer_name'
        );

        $this->projectInstance = $this->projectService->create(
            $this->userInstance['id'], 'project-name', '#FFFFFF'
        );

        $this->dateExample = date("Y-m-d H:i:s", strtotime('now'));
    }

    /** @test */
    public function can_create_task()
    {
        $response = $this->taskService->create(
            $this->userInstance['id'], $this->projectInstance['id'], '1',
            'title', 'description', $this->dateExample, $this->dateExample);
        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('id', $response));
    }

    /** @test */
    public function must_have_owner()
    {
        $this->expectException(APIException::class);
        $response = $this->taskService->create(
            $this->invalidId, $this->projectInstance['id'], '1',
            'title', 'description', $this->dateExample, $this->dateExample);

    }

    /** @test */
    public function can_have_null_project()
    {
        $response = $this->taskService->create(
            $this->userInstance['id'], null, '1',
            'title', 'description', $this->dateExample, $this->dateExample);
        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('id', $response));
    }

    /** @test */
    public function can_edit_tasks()
    {
        $task = $this->taskService->create(
            $this->userInstance['id'], null, '1',
            'title', 'description', $this->dateExample, $this->dateExample);

        $this->assertTrue(is_array($task));
        $this->assertTrue(array_key_exists('id', $task));

        $newResponse = $this->taskService->edit(
            $this->userInstance['id'], $task['id'], ['title' => 'new-title']
        );

        $this->assertTrue(array_key_exists('id', $newResponse));
        $this->assertTrue($newResponse['id'] === $task['id']);
        $this->assertTrue($newResponse['title'] === 'new-title');
    }

    /** @test */
    public function can_only_edit_own_tasks()
    {
        $anotherUser = $this->userService->create(
            'another-name', 'another-email', 'another-password', 'another-prefer_name'
        );

        $task = $this->taskService->create(
            $this->userInstance['id'], null, '1',
            'title', 'description', $this->dateExample, $this->dateExample);

        $this->assertTrue(is_array($task));
        $this->assertTrue(array_key_exists('id', $task));

        $this->expectException(APIException::class);
        $response = $this->taskService->edit(
            $anotherUser['id'], $task['id'], ['title' => 'new-title']
        );
    }

    /** @test */
    public function can_conclude_tasks()
    {
        $task = $this->taskService->create(
            $this->userInstance['id'], $this->projectInstance['id'], '1',
            'title', 'description', $this->dateExample, $this->dateExample);

        $this->assertTrue(is_array($task));
        $this->assertTrue(array_key_exists('id', $task));

        $wasDone = $this->taskService->markAsDone(
            $this->userInstance['id'], $task['id']
        );

        $this->assertTrue($wasDone === true);
    }

    /** @test */
    public function can_only_conclude_own_tasks()
    {
        $anotherUser = $this->userService->create(
            'another-name', 'another-email', 'another-password', 'another-prefer_name'
        );

        $task = $this->taskService->create(
            $this->userInstance['id'], null, '1',
            'title', 'description', $this->dateExample, $this->dateExample);

        $this->assertTrue(is_array($task));
        $this->assertTrue(array_key_exists('id', $task));

        $this->expectException(APIException::class);
        $response = $this->taskService->markAsDone(
            $anotherUser['id'], $task['id']
        );
    }

    /** @test */
    public function can_receive_concluded_tasks()
    {
        $task = $this->taskService->create(
            $this->userInstance['id'], $this->projectInstance['id'], '1',
            'title', 'description', $this->dateExample, $this->dateExample);

        $this->assertTrue(is_array($task));
        $this->assertTrue(array_key_exists('id', $task));

        $wasDone = $this->taskService->markAsDone(
            $this->userInstance['id'], $task['id']
        );

        $this->assertTrue($wasDone === true);

        $tasksDone = $this->taskService->getDoneTasks($this->userInstance['id']);
        $this->assertTrue(is_array($tasksDone));
        $this->assertTrue(count($tasksDone) === 1);
    }

    /** @test */
    public function can_only_include_tasks_in_owned_projects()
    {
        $anotherUser = $this->userService->create(
            'another-name', 'another-email', 'another-password', 'another-prefer_name'
        );

        $this->expectException(APIException::class);
        $task = $this->taskService->create(
            $anotherUser['id'], $this->projectInstance['id'], '1',
            'title', 'description', $this->dateExample, $this->dateExample);
    }

    /** @test */
    public function can_delete_tasks()
    {
        $task = $this->taskService->create(
            $this->userInstance['id'], $this->projectInstance['id'], '1',
            'title', 'description', $this->dateExample, $this->dateExample);

        $this->assertTrue(is_array($task));
        $this->assertTrue(array_key_exists('id', $task));

        $wasDeleted = $this->taskService->delete($this->userInstance['id'], $task['id']);
        $this->assertTrue($wasDeleted === true);
    }

    /** @test */
    public function can_only_delete_owned_tasks()
    {
        $task = $this->taskService->create(
            $this->userInstance['id'], $this->projectInstance['id'], '1',
            'title', 'description', $this->dateExample, $this->dateExample);

        $this->assertTrue(is_array($task));
        $this->assertTrue(array_key_exists('id', $task));

        $anotherUser = $this->userService->create(
            'another-name', 'another-email', 'another-password', 'another-prefer_name'
        );

        $this->expectException(APIException::class);
        $wasDeleted = $this->taskService->delete($anotherUser['id'],
            $task['id']
        );

    }

}
