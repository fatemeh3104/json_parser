<?php

namespace ProcessMaker\Package\Utils\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ProcessMaker\Package\Packages\Events\PackageEvent;
use ProcessMaker\Package\Utils\Http\Middleware\AddToMenus;
use ProcessMaker\Package\Utils\Http\Middleware\ValidationItems;
use ProcessMaker\Package\Utils\Http\Middleware\ValidationUpdate;
use ProcessMaker\Package\Utils\Listeners\PackageListener;
use ProcessMaker\Package\Utils\Console\Commands\Install;
use ProcessMaker\Package\Utils\Console\Commands\Uninstall;

class PackageServiceProvider extends ServiceProvider
{

    /**
     * If your plugin will provide any services, you can register them here.
     * See: https://laravel.com/docs/5.6/providers#the-register-method
     */
    public function register()
    {


    }

    /**
     * After all service provider's register methods have been called, your boot method
     * will be called. You can perform any initialization code that is dependent on
     * other service providers at this time.  We've included some example behavior
     * to get you started.
     *
     * See: https://laravel.com/docs/5.6/providers#the-boot-method
     */
    public function boot()
    {
        //Register commands
        $this->commands([
            Install::class,
            Uninstall::class,
        ]);
        $this->app->register(RouteServiceProvider::class);

//        $kernel->pushMiddleware(ValidationItems::class);
        app('router')->aliasMiddleware('ValidationItems', ValidationItems::class);
        app('router')->aliasMiddleware('ValidationUpdate', ValidationUpdate::class);

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/processmaker/packages/utils'),
        ], 'utils');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->app['events']->listen(PackageEvent::class, PackageListener::class);

    }
}
