<?php

use App\Services\UserService;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SessionControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserService::class);
        $this->userService->create(
            'name', 'email@gmail.com', 'password', 'prefer_name'
        );
    }

    /** @test */
    public function api_can_create_session()
    {
        $response = $this->post('/api/users/login', [
            'email' => 'email@gmail.com',
            'password' => 'password',
        ])->seeJsonStructure([
            'token',
        ]);
    }

    /** @test */
    public function api_sends_token_on_create()
    {
        $response = $this->post('/api/users/login', [
            'email' => 'email@gmail.com',
            'password' => 'password',
        ])->response;

        $this->assertTrue(
            isset($response['token'])
        );

        $this->assertTrue(
            isset($response['token_type'])
        );

        $this->assertTrue(
            isset($response['expires_in'])
        );
    }
}
