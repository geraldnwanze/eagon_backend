<?php

namespace App\Http\Middleware\API;

use App\Enums\RoleEnum;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // if ($request->user() && $request->user()->role != RoleEnum::SUPER_ADMIN->value) {
        //     if (!$request->header('tenant_key')) {
        //         return response()->json([
        //             'message' => 'Tenant key is required'
        //         ], Response::HTTP_FORBIDDEN);
        //     }

        //     if (!Tenant::where('key', $request->header('tenant_key'))->exists()) {
        //         return response()->json([
        //             'message' => 'Invalid Tenant key'
        //         ], Response::HTTP_FORBIDDEN);
        //     }
        // }

        // if (!$request->user() && $request->input('email') !== 'superadmin@example.com') {
        //     if (!$request->header('tenant_key')) {
        //         return response()->json([
        //             'message' => 'Tenant key is required'
        //         ], Response::HTTP_FORBIDDEN);
        //     }

        //     if (!Tenant::where('key', $request->header('tenant_key'))->exists()) {
        //         return response()->json([
        //             'message' => 'Invalid Tenant key'
        //         ], Response::HTTP_FORBIDDEN);
        //     }
        // }

        return $next($request);
    }
}
