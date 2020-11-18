<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function api_can_create_user()
    {
        $this->post('/api/users', [
            'name' => 'user-name',
            'email' => 'user-email@gmail.com',
            'password' => 'user-password',
            'password_confirmation' => 'user-password',
        ])->seeJson([
            'message' => 'CREATED',
        ]);
    }

    /** @test */
    public function api_must_require_password_confirmation()
    {
        $this->post('/api/users', [
            'name' => 'user-name',
            'email' => 'user-email@gmail.com',
            'password' => 'user-password',
        ])->seeJsonStructure([
            'password',
        ]);

        $this->post('/api/users', [
            'name' => 'user-name',
            'email' => 'user-email@gmail.com',
            'password' => 'user-password',
            'password_confirmation' => 'wrong-user-password',
        ])->seeJsonStructure([
            'password',
        ]);
    }

}
