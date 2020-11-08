<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Services\SessionService;

class SessionController extends Controller
{
    private $sessionService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        try {

            $token = $this->sessionService->create($email, $password);

            return response()->json([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
            ], 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorized.'], 401);

        }
    }
}
