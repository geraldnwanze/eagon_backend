<?php

namespace App\Providers;

use App\Models\Estate;
use App\Models\User;
use App\Observers\AdminObserver;
use App\Observers\EstateObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Estate::observe(EstateObserver::class);
        User::observe(AdminObserver::class);

        Scramble::configure()->withOperationTransformers(function (Operation $operation) {
            $operation->addParameters([
                new Parameter('tenant_key', 'header')
            ]);
        })->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });
    }
}
