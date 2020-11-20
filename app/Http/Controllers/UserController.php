<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function create(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $preferName = $request->input('prefer_name') ?? '';
        try {

            $user = $this->userService->create($name, $email, $password, $preferName);

            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function get()
    {
        return response()->json(auth('api')->user(), 200);
    }

    public function edit(Request $request)
    {
        $user = auth('api')->user();

        try {

            $user = $this->userService->edit($user->id, $request->all());

            return response()->json(['user' => $user, 'message' => 'EDITED'], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to edit user information'], 409);
        }
    }

    public function delete()
    {
        $user = auth('api')->user();

        try {

            $this->userService->delete($user->id);

            return response()->json(['message' => 'DELETED'], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user'], 409);
        }

        return response()->json([], 200);
    }

}
