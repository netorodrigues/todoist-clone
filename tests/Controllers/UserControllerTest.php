<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function get_user_token()
    {
        $this->post('/api/users', [
            'name' => 'user-name',
            'email' => 'user-email@gmail.com',
            'password' => 'user-password',
            'password_confirmation' => 'user-password',
        ])->seeJson([
            'message' => 'CREATED',
        ]);

        $sessionResponse = $this->post('/api/users/login', [
            'email' => 'user-email@gmail.com',
            'password' => 'user-password',
        ])->response;

        return $sessionResponse['token'];
    }

    /** @test */
    public function api_can_create_user()
    {
        $this->post('/api/users', [
            'name' => 'user-name',
            'email' => 'user-email@gmail.com',
            'password' => 'user-password',
            'password_confirmation' => 'user-password',
            'prefer_name' => 'prefered-name',
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
        ])->seeJsonStructure([
            'password',
        ]);

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

    /** @test */
    public function api_must_require_name_and_email()
    {
        $this->post('/api/users', [
            'email' => 'user-email@gmail.com',
            'password' => 'user-password',
            'password_confirmation' => 'user-password',
        ])->seeJsonStructure([
            'name',
        ]);

        $this->post('/api/users', [
            'name' => 'user-name',
            'password' => 'user-password',
            'password_confirmation' => 'wrong-user-password',
        ])->seeJsonStructure([
            'email',
        ]);

        $this->post('/api/users', [
            'name' => 'user-name',
            'email' => 'wrong-email-format',
            'password' => 'user-password',
            'password_confirmation' => 'wrong-user-password',
        ])->seeJsonStructure([
            'email',
        ]);
    }

    /** @test */
    public function api_must_consider_prefer_name_optional()
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
    public function api_must_allow_user_to_be_edited()
    {
        $token = $this->get_user_token();

        $this->put('/api/users', [
            'name' => 'new-user-name',
        ], [
            'Authorization' => "Bearer {$token}",
        ])->seeJson([
            'message' => 'EDITED',
        ]);

    }

    /** @test */
    public function api_must_deny_edit_request_with_invalid_token()
    {
        $this->put('/api/users', [
            'name' => 'new-user-name',
        ], [
            'Authorization' => "Bearer invalid-token",
        ])->seeJson([
            'status' => 'Authorization Token not defined or invalid',
        ]);
    }

    /** @test */
    public function api_must_allow_user_to_be_deleted()
    {
        $token = $this->get_user_token();

        $this->delete('/api/users', [], [
            'Authorization' => "Bearer {$token}",
        ])->seeJson([
            'message' => 'DELETED',
        ]);
    }

    /** @test */
    public function api_must_deny_delete_request_with_invalid_token()
    {
        $this->delete('/api/users', [], [
            'Authorization' => "Bearer invalid-token",
        ])->seeJson([
            'status' => 'Authorization Token not defined or invalid',
        ]);
    }

}
