<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Services\UserService;

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
        {
            //validate incoming request
            $this->validate($request, [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
            ]);

            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');

            try {

                $user = $this->userService->create($name, $email, $password);

                return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

            } catch (\Exception $e) {
                return response()->json(['message' => 'User Registration Failed!'], 409);
            }
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(auth('api')->user(), 200);
    }

}
