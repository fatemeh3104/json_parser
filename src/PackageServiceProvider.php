<?php

namespace ProcessMaker\Package\Parssconfig;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ProcessMaker\Package\Packages\Events\PackageEvent;
use ProcessMaker\Package\Parssconfig\Http\Middleware\AddToMenus;
use ProcessMaker\Package\Parssconfig\Http\Middleware\ValidationItems;
use ProcessMaker\Package\Parssconfig\Http\Middleware\ValidationUpdate;
use ProcessMaker\Package\Parssconfig\Listeners\PackageListener;

class PackageServiceProvider extends ServiceProvider
{

    // Assign the default namespace for our controllers
    protected $namespace = '\ProcessMaker\Package\Parssconfig\Http\Controllers';

    /**
     * If your plugin will provide any services, you can register them here.
     * See: https://laravel.com/docs/5.6/providers#the-register-method
     */
    public function register()
    {
        // Nothing is registered at this time
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
            Console\Commands\Install::class,
            Console\Commands\Uninstall::class,
            Console\Commands\ShowItems::class
        ]);

        if ($this->app->runningInConsole()) {
            require(__DIR__ . '/../routes/console.php');
        } else {
            // Assigning to the web middleware will ensure all other middleware assigned to 'web'
            // will execute. If you wish to extend the user interface, you'll use the web middleware
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(__DIR__ . '/../routes/web.php');


            Route::middleware('api')
                ->namespace($this->namespace)
                ->prefix('api/1.0')
                ->group(__DIR__ . '/../routes/api.php');

        }
//        $kernel->pushMiddleware(ValidationItems::class);
        app('router')->aliasMiddleware('ValidationItems', ValidationItems::class);
        app('router')->aliasMiddleware('ValidationUpdate', ValidationUpdate::class);
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'parssconfig');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/processmaker/packages/parssconfig'),
        ], 'parssconfig');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app['events']->listen(PackageEvent::class, PackageListener::class);

    }
}
