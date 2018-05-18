<?php
namespace stoykov\Ohrana;

use Illuminate\Support\ServiceProvider;

class OhranaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerMiddleware();

        $this->app->singleton('ohrana', function($app) {
            return new ACLContainer($app);
        });
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register package's configuration.
     */
    public function registerConfig()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($configPath, 'ohrana');
        $this->publishes([
            $configPath => config_path('ohrana.php'),
        ], 'config');
        $this->app->configure('ohrana');
    }

    public function registerMiddleware()
    {
        $this->app->routeMiddleware([
            'ohrana' => \stoykov\Ohrana\Middleware\OhranaMiddleware::class,
        ]);
    }
}
