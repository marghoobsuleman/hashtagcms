<?php

namespace MarghoobSuleman\HashtagCms\Core\Providers;

use MarghoobSuleman\HashtagCms\Core\Common;
use Illuminate\Support\ServiceProvider;


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
        $this->app->singleton("Common", function() {
            return new Common();
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

    public function process() {

    }
}
