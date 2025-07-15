<?php

namespace App\Http\Controllers\API;

use App\Enums\RoleEnum;
use App\Helpers\ApiResponse;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\AdminLoginRequest;
use App\Http\Requests\API\Auth\ResidentLoginRequest;
use App\Http\Requests\API\Auth\SuperAdminLoginRequest;
use App\Http\Resources\UserResource;
use App\Jobs\FirstLoginJob;
use App\Mail\TestMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function residentLogin(ResidentLoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
            return ApiResponse::failure('Invalid Credentials');
        }

        if ($user->role === RoleEnum::ADMIN->value || $user->role === RoleEnum::SUPER_ADMIN->value) {
            return ApiResponse::failure('Unauthorized', statusCode: 401);
        }

        if (!$user->is_active) {
            return ApiResponse::failure('Unauthorized, contact Admin', statusCode: Response::HTTP_UNAUTHORIZED);
        }

        if ($user->is_first_login) {
            $code = CommonHelper::generateOTP($user);
            FirstLoginJob::dispatch($user, $code);
            return ApiResponse::success('Verification code has been sent to  your email');
        }

        if ($request->validated('fcm_token')) {
            $user->update(['fcm_token' => $request->validated('fcm_token')]);
        }

        $user->tokens()->delete();
        $token = $user->createToken(config('app.name'))->plainTextToken;

        return ApiResponse::success('Login successful', [
            'auth_token' => $token,
            'user' => new UserResource($user)
        ]);
    }

    public function adminLogin(AdminLoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
            return ApiResponse::failure('Invalid Credentials');
        }

        if ($user->role !== RoleEnum::ADMIN->value) {
            return ApiResponse::failure('Unauthorized', statusCode: 401);
        }

        if (!$user->is_active) {
            return ApiResponse::failure('Unauthorized, contact Admin', statusCode: Response::HTTP_UNAUTHORIZED);
        }

        if ($user->role !== RoleEnum::ADMIN->value) {
            return ApiResponse::failure('Unauthorized', statusCode: Response::HTTP_UNAUTHORIZED);
        }

        if ($request->validated('fcm_token')) {
            $user->update(['fcm_token' => $request->validated('fcm_token')]);
        }

        $user->tokens()->delete();
        $token = $user->createToken(config('app.name'))->plainTextToken;

        return ApiResponse::success('Login successful', [
            'auth_token' => $token,
            'user' => new UserResource($user)
        ]);
    }

    public function superAdminLogin(SuperAdminLoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
            return ApiResponse::failure('Invalid Credentials');
        }

        if ($user->role !== RoleEnum::SUPER_ADMIN->value) {
            return ApiResponse::failure('Unauthorized', statusCode: 401);
        }

        if (!$user->is_active) {
            return ApiResponse::failure('Unauthorized, contact Admin', statusCode: Response::HTTP_UNAUTHORIZED);
        }

        if ($user->role !== RoleEnum::SUPER_ADMIN->value) {
            return ApiResponse::failure('Unauthorized', statusCode: Response::HTTP_UNAUTHORIZED);
        }

        if ($request->validated('fcm_token')) {
            $user->update(['fcm_token' => $request->validated('fcm_token')]);
        }

        $user->tokens()->delete();
        $token = $user->createToken(config('app.name'))->plainTextToken;

        return ApiResponse::success('Login successful', [
            'auth_token' => $token,
            'user' => new UserResource($user)
        ]);
    }

    public function residentForgotPassword()
    {

    }

    public function residentResetPassword()
    {

    }

    public function residentVerifyEmail()
    {

    }

}
