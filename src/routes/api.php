<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$callable = config("hashtagcms.namespace")."Http\Controllers\Api\\";

Route::middleware('api')->prefix("api/hashtagcms")->group(function() use($callable) {

    /**
     * Health check
     */
    Route::get("health-check", function(Request $request) {
        return array("result"=>"okay");
    });

    Route::post('public/user/v1/register', function (Request $request) use($callable)  {

        return app()->call($callable."AuthController@register");

    });

    Route::post('public/user/v1/login', function (Request $request) use($callable) {

        return app()->call($callable."AuthController@login");

    });

    /**
     * Mobile Splash Screen
     * Used for config
     */
    Route::get("public/mobiles/configs/v1/site-configs", function (Request $request)  use($callable) {

        return app()->call($callable."ServiceController@siteConfigs");

    });


    //Load category data
    Route::get("public/sites/v1/load-data", function(Request $request) use($callable) {

        return app()->call($callable."ServiceController@loadData");

    });

    /**
     * Load a module
     */
    Route::get("public/service/v1/load-module", function(Request $request) use($callable) {

        return app()->call($callable."ServiceController@loadModule");

    });

    /**
     * Load a module
     */
    Route::get("public/service/v1/load-hook", function(Request $request) use($callable) {

        return app()->call($callable."ServiceController@loadHook");

    });

    /**
     * Load a module
     */
    Route::get("public/service/v1/test", function(Request $request) use($callable) {

        return app()->call($callable."ServiceController@test");

    });

});

//Authentication
Route::middleware('auth:api')->prefix("api/hashtagcms")->group(function() use($callable) {

    Route::get("user/v1/me", function (Request $request) use($callable) {

        return app()->call($callable."AuthController@me");

    });
});
