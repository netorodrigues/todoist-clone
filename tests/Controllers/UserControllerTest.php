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
        // case must be implemented
        $this->assertTrue(false);
    }

    /** @test */
    public function api_must_deny_edit_request_with_invalid_token()
    {
        // case must be implemented
        $this->assertTrue(false);
    }

    /** @test */
    public function api_must_allow_user_to_be_deleted()
    {
        // case must be implemented
        $this->assertTrue(false);
    }

    /** @test */
    public function api_must_deny_delete_request_with_invalid_token()
    {
        // case must be implemented
        $this->assertTrue(false);
    }

}
