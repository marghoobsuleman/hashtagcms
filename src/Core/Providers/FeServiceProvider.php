<?php

namespace MarghoobSuleman\HashtagCms\Core\Providers;

use Illuminate\Support\ServiceProvider;
use MarghoobSuleman\HashtagCms\Core\Common;
use MarghoobSuleman\HashtagCms\Core\Main\DataLoader;
use MarghoobSuleman\HashtagCms\Core\Main\InfoLoader;
use MarghoobSuleman\HashtagCms\Core\Main\LayoutManager;
use MarghoobSuleman\HashtagCms\Core\Main\ModuleLoader;
use MarghoobSuleman\HashtagCms\Core\Main\SessionManager;

class FeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        //This will have useful things
        $this->app->singleton('HashtagCms', function () {
            return new Common();
        });

        $this->app->singleton('HashtagCmsInfoLoader', function () {
            return new InfoLoader();
        });

        $this->app->singleton('HashtagCmsModuleLoader', function () {
            return new ModuleLoader();
        });

        $this->app->singleton('HashtagCmsLayoutManager', function () {
            return new LayoutManager();
        });

        $this->app->singleton('HashtagCmsCache', function () {
            return new SessionManager();
        });

        $this->app->singleton('HashtagCmsDataLoader', function () {
            return new DataLoader();
        });

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    public function process()
    {

    }
}
