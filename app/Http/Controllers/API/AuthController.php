<?php

namespace App\Http\Controllers\API;

use App\Enums\RoleEnum;
use App\Helpers\ApiResponse;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\AdminLoginRequest;
use App\Http\Requests\API\Auth\ResidentLoginRequest;
use App\Http\Requests\API\Auth\ResidentVerifyEmailRequest;
use App\Http\Requests\API\Auth\SuperAdminLoginRequest;
use App\Http\Resources\UserResource;
use App\Jobs\FirstLoginJob;
use App\Mail\TestMail;
use App\Models\OTP;
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
            FirstLoginJob::dispatchSync($user, $code);
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

    public function residentVerifyEmail(ResidentVerifyEmailRequest $request)
    {
        $user = User::where(['email' => $request->validated('email'), 'tenant_key' => $request->header('tenant_key')])->first();
        $otp = OTP::where(['user' => $user->id, 'tenant_key' => $request->header('tenant_key')])->first();

        if (!$otp || $otp->code != $request->validated('otp') || $otp->expires_at <= now()) {
            return ApiResponse::failure('Invalid OTP provided');
        }

        $otp->update([
            'confirmation_token' => \Illuminate\Support\Str::random(32)
        ]);

        $user->update([
            'email_verified_at' => now(),
            'is_first_login' => false
        ]);

        return ApiResponse::success('Email verified', [
                'confirmation_token' => $otp->confirmation_token
            ]);
    }

}
