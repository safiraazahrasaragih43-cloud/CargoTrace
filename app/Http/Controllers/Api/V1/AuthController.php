<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ApiLoginRequest;
use App\Http\Requests\Auth\ApiRegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user via API.
     */
    public function register(ApiRegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());
        $deviceName = $request->input('device_name', 'mobile-or-api');
        $token = $this->authService->createApiToken($user, $deviceName);

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'User registered successfully', 201);
    }

    /**
     * Authenticate user credentials and return Sanctum token.
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->authenticate(
                $request->input('email'),
                $request->input('password')
            );
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        }

        $deviceName = $request->input('device_name', 'mobile-or-api');
        $token = $this->authService->createApiToken($user, $deviceName);

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Logged in successfully');
    }

    /**
     * Return authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => new UserResource($request->user()),
        ], 'User profile retrieved successfully');
    }

    /**
     * Revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->revokeCurrentToken($request->user());

        return $this->successResponse(null, 'Logged out successfully');
    }
}
