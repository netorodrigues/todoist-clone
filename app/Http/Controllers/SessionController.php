<?php

namespace App\Http\Controllers;

use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);

        }
    }
}
