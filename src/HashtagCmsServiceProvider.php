<?php

namespace MarghoobSuleman\HashtagCms;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;


use MarghoobSuleman\HashtagCms\Core\Middleware\Admin\BeMiddleware;
use MarghoobSuleman\HashtagCms\Core\Middleware\Admin\CmsModuleInfo;
use MarghoobSuleman\HashtagCms\Core\Middleware\FeMiddleware;

use MarghoobSuleman\HashtagCms\Core\Middleware\API\ETag;

use MarghoobSuleman\HashtagCms\Console\Commands\CmsModuleModelCommand;
use MarghoobSuleman\HashtagCms\Console\Commands\CmsModuleControllerCommand;
use MarghoobSuleman\HashtagCms\Console\Commands\CmsValidatorCommand;
use MarghoobSuleman\HashtagCms\Console\Commands\CmsInstall;

use MarghoobSuleman\HashtagCms\Core\Providers\Admin\AdminServiceProvider;
use MarghoobSuleman\HashtagCms\Core\Providers\FeServiceProvider;


class HashtagCmsServiceProvider extends ServiceProvider
{
    protected $groupName = 'hashtagcms';


    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

        //More providers for admin and frontend
        $this->app->register(AdminServiceProvider::class);
        $this->app->register(FeServiceProvider::class);

        //Middleware for Admin
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('cmsInterceptor', BeMiddleware::class);
        $router->aliasMiddleware('cmsModuleInfo', CmsModuleInfo::class);

        //Middleware for Frontend
        $router->aliasMiddleware('interceptor', FeMiddleware::class);
        $router->aliasMiddleware('etag', ETag::class);

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'hashtagcms');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'hashtagcms');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
        $this->bootForControllerToo();

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/hashtagcms.php', $this->groupName);
        $this->mergeConfigFrom(__DIR__.'/../config/hashtagcmsadmin.php', $this->groupName.'admin');

        // Register the service the package provides.
        $this->app->singleton('hashtagcms', function ($app) {
            return new HashtagCms;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hashtagcms'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/hashtagcms.php' => config_path('hashtagcms.php'),
            __DIR__.'/../config/hashtagcmsadmin.php' => config_path('hashtagcmsadmin.php'),
        ], $this->groupName.'.config');

        // Publishing the views.
        $this->publishes([
            __DIR__.'/../resources/views/fe' => resource_path('views/vendor/hashtagcms/fe'),
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/hashtagcms'),
        ], $this->groupName.'.views.frontend');


        $this->publishes([
            __DIR__.'/../resources/views/be' => resource_path('views/vendor/hashtagcms/be'),
            __DIR__.'/../resources/assets/be' => resource_path('assets/hashtagcms/be')
        ], $this->groupName.'.views.admin');

        $this->publishes([
            __DIR__.'/../resources/views/be/default/common' => resource_path('views/vendor/hashtagcms/be/default/common'),
            __DIR__.'/../resources/views/be/default/index.blade.php' => resource_path('views/vendor/hashtagcms/be/default/index.blade.php')
        ], $this->groupName.'.views.admincommon');

        //Export view and js for admin and frontend
        $this->publishes([
            __DIR__.'/../resources/views/be' => resource_path('views/vendor/hashtagcms/be'),
            __DIR__.'/../resources/views/fe' => resource_path('views/vendor/hashtagcms/fe'),
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/hashtagcms'),
            __DIR__.'/../resources/assets' => resource_path('assets/hashtagcms')

        ], $this->groupName.'.views.all');


        // Publishing assets.
        $this->publishes([
            __DIR__.'/../public/assets' => public_path('assets/hashtagcms'),
            __DIR__.'/../resources/assets/fe' => resource_path('assets/hashtagcms/fe'),
            __DIR__.'/../resources/assets/be' => resource_path('assets/hashtagcms/be'),
            __DIR__.'/../resources/assets/js' => resource_path('assets/hashtagcms/js'),
        ], $this->groupName.'.assets');

        // Registering package commands.
        $this->commands([
            CmsInstall::class,
            CmsValidatorCommand::class
        ]);

    }

    /**
     * Add some commands
     */
    protected function bootForControllerToo() {
        // Registering package commands.
        $this->commands([
            CmsModuleModelCommand::class,
            CmsModuleControllerCommand::class
        ]);
    }

}

