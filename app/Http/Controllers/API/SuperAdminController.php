<?php

namespace App\Http\Controllers\API;

use App\Enums\RoleEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\CreateAdminRequest;
use App\Http\Requests\API\Admin\CreateEstateRequest;
use App\Http\Resources\EstateResource;
use App\Http\Resources\UserResource;
use App\Jobs\WelcomeAndAuthDetailJob;
use App\Models\Estate;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function listEstates()
    {
        $estates = Estate::paginate();
        return ApiResponse::success('Estates fetched successfully', [
            'estates' => EstateResource::collection($estates)
        ]);
    }

    public function createEstate(CreateEstateRequest $request)
    {
        $estate = Estate::create([
            'name' => $request->input('estate_name'),
            'full_address' => $request->input('estate_address'),
            'longitude' => $request->input('longitude'),
            'latitude' => $request->input('latitude')
        ]);
        return ApiResponse::success("Estate successfully", [
            'estate' => new EstateResource($estate)
        ]);
    }

    public function removeEstate(Estate $estate)
    {
        $estate->delete();
        return ApiResponse::success('Estate Removed successfully');
    }

    public function listAdmins()
    {
        $admins = User::with('tenant')->where('role', RoleEnum::ADMIN->value)
                    ->paginate();

        return ApiResponse::success('Admins fetched successfully', [
            'admins' => UserResource::collection($admins)
        ]);
    }

    public function createAdmin(CreateAdminRequest $request)
    {
        $tenant = Tenant::where('estate_id', $request->validated('estate_id'))->first();
        $data = array_merge($request->validated(), [
            'role' => RoleEnum::ADMIN,
            'invited_by' => $request->user()->id,
            'tenant_key' => $tenant->key
        ]);

        unset($data['estate_id']);

        $admin = User::create($data);

        WelcomeAndAuthDetailJob::dispatchSync($admin, $request->validated('password'));

        return ApiResponse::success("Sign in details have been sent to {$admin->email}");
    }

    public function removeAdmin(User $admin)
    {
        if ($admin->role === RoleEnum::SUPER_ADMIN->value) {
            return ApiResponse::failure('You cannot remove Super Admin');
        }

        $admin->delete();

        return ApiResponse::success('Admin Removed successfully');
    }
}
