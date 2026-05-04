<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Authentication
 *
 * Endpoints for obtaining and revoking API tokens (Laravel Sanctum).
 */
class AuthController extends Controller
{
    /**
     * Register
     *
     * Creates a new user account and returns a Sanctum API token.
     *
     * @bodyParam name string required Full name. Example: Jane Doe
     * @bodyParam email string required Email address. Example: jane@example.com
     * @bodyParam password string required Password (min 8 chars). Example: secret123
     * @bodyParam password_confirmation string required Must match password. Example: secret123
     *
     * @response 201 {"token": "1|abc123...", "token_type": "Bearer"}
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user  = User::create($data);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login
     *
     * Authenticates a user and returns a Sanctum API token.
     *
     * @bodyParam email string required Registered email. Example: admin@example.com
     * @bodyParam password string required Password. Example: password
     *
     * @response 200 {"token": "1|abc123...", "token_type": "Bearer"}
     * @response 422 {"message": "The provided credentials are incorrect.", "errors": {"email": ["The provided credentials are incorrect."]}}
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke all previous tokens (single-session policy)
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout
     *
     * Revokes the current API token.
     *
     * @authenticated
     *
     * @response 200 {"message": "Logged out successfully."}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Me
     *
     * Returns the currently authenticated user.
     *
     * @authenticated
     *
     * @response 200 {"id": 1, "name": "Jane Doe", "email": "jane@example.com"}
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->only(['id', 'name', 'email']));
    }
}
