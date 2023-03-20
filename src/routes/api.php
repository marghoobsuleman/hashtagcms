<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$callable = config("hashtagcms.namespace")."Http\Controllers\Api\\";

Route::middleware(['api', 'etag'])->prefix("api/hashtagcms")->group(function() use($callable) {

    /**
     * Health check
     */
    Route::get("health-check", function(Request $request) {
        return array("result"=>"okay");
    });

    /**
     * Registration: V1
     */
    Route::post('public/user/v1/register', function (Request $request) use($callable)  {
        return app()->call($callable."AuthController@register");
    });

    /**
     * Login: V1
     */
    Route::post('public/user/v1/login', function (Request $request) use($callable) {
        return app()->call($callable."AuthController@login");
    });

    /**
     * Site config
     */
    Route::get("public/configs/v1/site-configs", function (Request $request)  use($callable) {

        return app()->call($callable."ServiceControllerV2@siteConfigs");

    });

    /**
     * Load data
     */
    Route::get("public/sites/v1/load-data", function(Request $request) use($callable) {

        return app()->call($callable."ServiceControllerV2@loadData");

    });

    /**
     * Load a module
     * @deprecated
     */
    Route::get("public/service/v1/load-module", function(Request $request) use($callable) {

        return app()->call($callable."ServiceController@loadModule");

    });

    /**
     * Load a hook
     * @deprecated
     */
    Route::get("public/service/v1/load-hook", function(Request $request) use($callable) {

        return app()->call($callable."ServiceController@loadHook");

    });



});

//Authentication
Route::middleware('auth:api')->prefix("api/hashtagcms")->group(function() use($callable) {

    Route::get("user/v1/me", function (Request $request) use($callable) {
        
        return app()->call($callable."AuthController@me");

    });
});
