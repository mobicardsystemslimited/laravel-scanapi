<?php

namespace MobicardApi\ScanApi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use MobicardApi\ScanApi\Http\Middleware\ValidateScanApiConfig;

class ScanApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/Config/scanapi.php',
            'scanapi'
        );

        // Register the main service class
        $this->app->singleton('scanapi', function ($app) {
            return new Services\ScanApiService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/views', 'scanapi');

        // Register middleware
        $router->aliasMiddleware('scanapi.config', ValidateScanApiConfig::class);

        // Publish configuration
        $this->publishes([
            __DIR__ . '/Config/scanapi.php' => config_path('scanapi.php'),
        ], 'scanapi-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/scanapi'),
        ], 'scanapi-views');

        // Publish assets (if you have any CSS/JS files)
        $this->publishes([
            __DIR__ . '/assets' => public_path('vendor/scanapi'),
        ], 'scanapi-assets');
    }
}
