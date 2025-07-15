<?php

namespace App\Traits;

use App\Enums\RoleEnum;
use App\Helpers\ApiResponse;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait TenancyTrait
{
    protected static function bootTenancyTrait()
    {
        // Skip tenant logic when running artisan or in console
        if (app()->runningInConsole()) {
            return;
        }

        // Throw exception if tenant_key is required but missing
        if (!request()->header('tenant_key')) {
            // throw new HttpException(Response::HTTP_FORBIDDEN, 'Tenant key is required');
            return ApiResponse::failure(Response::HTTP_FORBIDDEN);
        }

        $user = request()->user();
        $tenantKey = request()->header('tenant_key');

        // Global tenant_key scope
        static::addGlobalScope('tenant_key', function (Builder $builder) use ($tenantKey) {
            $builder->where('tenant_key', $tenantKey);
        });

        // Global user_id scope, only if applicable
        static::addGlobalScope('user_id', function (Builder $builder) use ($user) {
            if ($user && $user->role === RoleEnum::RESIDENT->value && Schema::hasColumn($builder->getModel()->getTable(), 'user_id')) {
                $builder->where('user_id', $user->id);
            }
        });

        static::creating(function ($model) use ($tenantKey, $user) {
            // Always set tenant_key if the column exists
            if (Schema::hasColumn($model->getTable(), 'tenant_key')) {
                $model->tenant_key = $tenantKey;
            }

            // Set estate_id only if tenant is found
            $tenant = \App\Models\Tenant::where('key', $tenantKey)->first();
            if ($tenant && Schema::hasColumn($model->getTable(), 'estate_id')) {
                $model->estate_id = $tenant->estate_id;
            }

            // Set user_id only if column exists and user is not SUPER_ADMIN or ADMIN
            if ($user && !in_array($user->role, [RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN])) {
                if (Schema::hasColumn($model->getTable(), 'user_id')) {
                    $model->user_id = $user->id;
                }
            }
        });
    }
}
