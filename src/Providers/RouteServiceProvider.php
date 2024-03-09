<?php

namespace ProcessMaker\Package\Parssconfig\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = '\RSO\ServiceDesk\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapConsoleRoutes();
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapConsoleRoutes(){
        require(__DIR__ . "/../../routes/console.php");
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../../routes/web.php');
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->namespace("{$this->namespace}\Api")
            ->prefix('api/1.0')
            ->middleware(['bindings','auth:api'])
            ->group(__DIR__ . '/../../routes/api.php');
    }
}
