<?php

namespace ProcessMaker\Package\Parssconfig\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ProcessMaker\Package\Packages\Events\PackageEvent;
use ProcessMaker\Package\Parssconfig\Http\Middleware\AddToMenus;
use ProcessMaker\Package\Parssconfig\Http\Middleware\ValidationItems;
use ProcessMaker\Package\Parssconfig\Http\Middleware\ValidationUpdate;
use ProcessMaker\Package\Parssconfig\Listeners\PackageListener;
use ProcessMaker\Package\Parssconfig\Console\Commands\Install;
use ProcessMaker\Package\Parssconfig\Console\Commands\Uninstall;

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
            __DIR__ . '/../public' => public_path('vendor/processmaker/packages/parssconfig'),
        ], 'parssconfig');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->app['events']->listen(PackageEvent::class, PackageListener::class);

    }
}
