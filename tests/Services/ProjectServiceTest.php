<?php

use App\Exceptions\APIException;
use App\Services\ProjectService;
use App\Services\UserService;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProjectServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $projectService;
    private $userService;
    private $userInstance;

    private $nonExistingId = '5aa73a44-2d83-11eb-adc1-0242ac120002';

    public function setUp(): void
    {
        parent::setUp();

        $this->userService = app(UserService::class);
        $this->projectService = app(ProjectService::class);
        $this->userInstance = $this->userService->create(
            'name', 'email', 'password', 'prefer_name'
        );
    }

    /** @test */
    public function can_create_project()
    {
        $project = $this->projectService->create(
            $this->userInstance['id'],
            "test_project",
            "#fff"
        );

        $this->assertTrue(is_array($project));
        $this->assertTrue(array_key_exists('id', $project));
    }

    /** @test */
    public function can_return_list_of_user_projects()
    {
        $project = $this->projectService->create(
            $this->userInstance['id'],
            "test_project",
            "#fff"
        );

        $projectList = $this->projectService->get(
            $this->userInstance['id']
        );

        $this->assertTrue(count($projectList) === 1);

        $project = $this->projectService->create(
            $this->userInstance['id'],
            "test_second_project",
            "#000"
        );

        $projectList = $this->projectService->get(
            $this->userInstance['id']
        );

        $this->assertTrue(count($projectList) === 2);

    }

    /** @test */
    public function cannot_create_project_with_invalid_color()
    {
        $this->expectException(APIException::class);
        $project = $this->projectService->create(
            $this->userInstance['id'],
            "test_project",
            "invalid-color"
        );
    }

    /** @test */
    public function cannot_create_project_for_invalid_user()
    {
        $this->expectException(APIException::class);
        $response = $this->projectService->create(
            $this->nonExistingId,
            'invalid-project',
            '#fff'
        );
    }

    /** @test */
    public function can_edit_existing_project()
    {
        $project = $this->projectService->create(
            $this->userInstance['id'],
            "test_project",
            "#fff"
        );

        $this->assertTrue(is_array($project));

        $updatedProject = $this->projectService->edit(
            $this->userInstance['id'],
            $project['id'],
            array("name" => "new_test_project_name", "color" => "#000")
        );

        $this->assertTrue(is_array($updatedProject));

        $this->assertTrue(
            $updatedProject['name'] === 'new_test_project_name'
        );

        $this->assertTrue($updatedProject['color'] === '#000');
    }

    /** @test */
    public function cannot_edit_non_existing_project()
    {
        $this->expectException(APIException::class);
        $editedUser = $this->projectService->edit(
            $this->userInstance['id'],
            $this->nonExistingId,
            ['email' => 'new-email']
        );
    }

    /** @test */
    public function cannot_edit_project_for_non_existing_user()
    {
        $this->expectException(APIException::class);
        $editedUser = $this->projectService->edit(
            $this->nonExistingId,
            $this->nonExistingId,
            ['email' => 'new-email']
        );
    }

    /** @test */
    public function cannot_edit_other_user_project()
    {
        $project = $this->projectService->create(
            $this->userInstance['id'],
            "test_project",
            "#fff"
        );

        $this->assertTrue(is_array($project));

        $anotherUser = $this->userService->create(
            'another-user',
            'another-email',
            'another-password',
            'another_prefer_name'
        );

        $this->expectException(APIException::class);
        $response = $this->projectService->edit(
            $anotherUser['id'],
            $project['id'],
            ["name" => 'forbidden-project-edit', "color" => '#000']
        );
    }

    /** @test */
    public function can_delete_project()
    {
        $project = $this->projectService->create(
            $this->userInstance['id'],
            "test_project",
            "#fff"
        );

        $this->assertTrue(is_array($project));

        $wasDeleted = $this->projectService->delete(
            $this->userInstance['id'],
            $project['id']
        );

        $this->assertTrue($wasDeleted === true);
    }

    /** @test */
    public function receive_same_response_trying_to_delete_non_existing_user()
    {
        $wasDeleted = $this->userService->delete(
            $this->userInstance["id"],
            $this->nonExistingId
        );

        $this->assertTrue($wasDeleted === true);
    }

}
