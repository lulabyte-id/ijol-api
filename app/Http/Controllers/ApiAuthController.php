<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TokenService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class ApiAuthController extends Controller
{
    protected TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Register a User.
     *
     * @return JsonResponse
     */
    public function register(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = new User();
            $user->name = request()->name;
            $user->email = request()->email;
            $user->password = bcrypt(request()->password);
            $user->save();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($user);
    }


    /**
     * Get a token via given credentials.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $loginAttempt = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        if (!$loginAttempt) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user = Auth::user();
            $tokens = $this->tokenService->createTokens($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($tokens);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $accessToken = $this->tokenService->createAccessToken($request->user());

        return response()->json([
                'message' => "New access token successfully generated",
                'token' => $accessToken->plainTextToken
            ]
        );
    }
}
