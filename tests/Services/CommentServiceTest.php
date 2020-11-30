<?php

use App\Services\CommentService;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Services\UserService;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CommentServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $commentService;
    private $userService;
    private $projectService;
    private $taskService;

    private $userInstance;
    private $projectInstance;
    private $taskInstance;

    private $invalidId = '5aa73a44-2d83-11eb-adc1-0242ac120002';

    public function setUp(): void
    {
        parent::setUp();

        $this->commentService = app(CommentService::class);
        $this->projectService = app(ProjectService::class);
        $this->userService = app(UserService::class);
        $this->taskService = app(TaskService::class);

        $this->userInstance = $this->userService->create(
            'name', 'email', 'password', 'prefer_name'
        );

        $this->projectInstance = $this->projectService->create(
            $this->userInstance['id'], 'project_name', '#FFFFFF'
        );

        $this->taskInstance = $this->taskService->create(
            $this->userInstance['id'], $this->projectInstance['id'], '1',
            'task title', 'task description', null, null
        );
    }

    /** @test */
    public function can_create_comment_for_project()
    {
        $response = $this->commentService->createForProject(
            $this->userInstance['id'], $this->projectInstance['id'], 'comment content'
        );

        $this->assertTrue(is_array($response));
    }

    /** @test */
    public function can_create_comment_for_task()
    {

        $response = $this->commentService->createForTask(
            $this->userInstance['id'], $this->taskInstance['id'], 'comment content'
        );

        $this->assertTrue(is_array($response));
    }

    /** @test */
    public function cannot_create_comments_for_invalid_user()
    {
        $this->expectException(APIException::class);
        $this->commentService->createForTask(
            $this->invalidId,
            $this->taskInstance['id'],
            'comment content'
        );
    }

    /** @test */
    public function cannot_create_comments_for_invalid_project()
    {

        $this->expectException(APIException::class);
        $this->commentService->createForProject(
            $this->invalidId,
            $this->projectInstance['id'],
            'comment content'
        );
    }

    /** @test */
    public function can_create_comments_in_other_users_projects()
    {
        $anotherUser = $this->userService->create(
            'another-user-name', 'another-email', 'another-password', 'prefer_name'
        );

        $response = $this->commentService->createForProject(
            $anotherUser['id'],
            $this->projectInstance['id'],
            'comment content'
        );

        $this->assertTrue(is_array($response));

    }

    /** @test */
    public function can_get_comments_for_user()
    {

        $this->commentService->createForTask(
            $this->userInstance['id'], $this->taskInstance['id'], 'comment content'
        );

        $this->commentService->createForProject(
            $this->userInstance['id'], $this->projectInstance['id'], 'comment content'
        );

        $response = $this->commentService->getForUser($this->userInstance['id']);

        $this->assertTrue(count($response) === 2);

    }

    /** @test */
    public function can_get_comments_for_project()
    {
        $this->commentService->createForTask(
            $this->userInstance['id'], $this->taskInstance['id'], 'comment content'
        );

        $this->commentService->createForProject(
            $this->userInstance['id'], $this->projectInstance['id'], 'comment content'
        );

        $response = $this->commentService->getForProject($this->projectInstance['id']);

        $this->assertTrue(count($response) === 1);

    }

    /** @test */
    public function can_get_comments_for_task()
    {
        $this->commentService->createForTask(
            $this->userInstance['id'], $this->taskInstance['id'], 'comment content'
        );

        $this->commentService->createForTask(
            $this->userInstance['id'], $this->taskInstance['id'], 'another comment'
        );

        $this->commentService->createForProject(
            $this->userInstance['id'], $this->projectInstance['id'], 'comment content'
        );

        $response = $this->commentService->getForTask($this->taskInstance['id']);

        $this->assertTrue(count($response) === 2);

    }

}
