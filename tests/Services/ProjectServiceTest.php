<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Services\ProjectService;
use App\Services\UserService;

use App\Exceptions\APIException;

class ProjectServiceTest extends TestCase
{
    use DatabaseTransactions;

    private $projectService;
    private $userService;
    private $userInstance;

    private $invalidId = -1;

    public function setUp(): void{
        parent::setUp();

        $this->userService = app(UserService::class);
        $this->projectService = app(ProjectService::class);
        $this->userInstance = $this->userService->create('name', 'email', 'password', 'prefer_name');
    }

    /** @test */
    public function can_create_project()
    {
        $project = $this->projectService->create($this->userInstance['id'], "test_project", "#fff");
        $this->assertTrue(is_array($project));
        $this->assertTrue(array_key_exists('id', $project));
    }

    /** @test */
    public function cannot_create_project_for_invalid_user()
    {
        $this->expectException(APIException::class);
        $response = $this->projectService->create($this->invalidId, 'invalid-project', '#fff');
    }

    /** @test */
    public function can_edit_existing_project()
    {
        $project = $this->projectService->create($this->userInstance['id'], "test_project", "#fff");
        $this->assertTrue(is_array($project));

        $updatedProject = $this->projectService->edit($this->userInstance['id'], $project['id'],
        array("name" => "new_test_project_name", "color" => "#000"));

        $this->assertTrue(is_array($updatedProject));
        $this->assertTrue($updatedProject['name'] === 'new_test_project_name');
        $this->assertTrue($updatedProject['color'] === '#000');
    }

    /** @test */
    public function cannot_edit_non_existing_project()
    {
        $this->expectException(APIException::class);
        $editedUser = $this->projectService->edit($this->userInstance['id'], $this->nonExistingId, ['email' => 'new-email']);
    }

    /** @test */
    public function cannot_edit_project_for_non_existing_user()
    {
        $this->expectException(APIException::class);
        $editedUser = $this->projectService->edit($this->nonExistingId, $this->nonExistingId, ['email' => 'new-email']);
    }

    /** @test */
    public function can_delete_project()
    {
        $project = $this->projectService->create($this->userInstance['id'], "test_project", "#fff");
        $this->assertTrue(is_array($project));

        $wasDeleted = $this->projectService->delete($this->userInstance['id'], $project['id']);
        $this->assertTrue($wasDeleted === true);
    }

    /** @test */
    public function receive_same_response_trying_to_delete_non_existing_user()
    {
        $wasDeleted = $this->userService->delete($this->userInstance["id"], $this->nonExistingId);
        $this->assertTrue($wasDeleted === true);
    }




}
