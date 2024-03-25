<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$callable = config('hashtagcms.namespace')."Http\Controllers\Api\\";

/**
 * Health check
 */
Route::get('api/hashtagcms/health-check', function (Request $request) {
    return ['result' => 'okay'];
});

Route::middleware(['api', 'etag'])->prefix('api/hashtagcms/public')->group(function () use ($callable) {

    /**
     * Registration: V1
     */
    Route::post('user/v1/register', function (Request $request) use ($callable) {
        return app()->call($callable.'AuthController@register');
    });

    /**
     * Login: V1
     */
    Route::post('user/v1/login', function (Request $request) use ($callable) {
        //return array("result"=>"okay");
        return app()->call($callable.'AuthController@login');
    });

    /**
     * Site config
     */
    Route::get('configs/v1/site-configs', function (Request $request) use ($callable) {

        return app()->call($callable.'ServiceController@siteConfigs');

    });

    /**
     * Load data
     */
    Route::get('sites/v1/load-data', function (Request $request) use ($callable) {

        return app()->call($callable.'ServiceController@loadData');

    });

    /**
     * Load data for mobile
     */
    Route::get('sites/v1/load-data-mobile', function (Request $request) use ($callable) {

        return app()->call($callable.'ServiceController@loadDataMobile');

    });

    /**
     * Load a module
     */
    Route::get('service/v1/load-module', function (Request $request) {

        return ['result' => 'will be available in later version'];

    });

    /**
     * Load a hook
     */
    Route::get('service/v1/load-hook', function (Request $request) {

        return ['result' => 'will be available in later version'];

    });

});

//Authentication
Route::middleware(['api', 'auth:sanctum'])->prefix('api/hashtagcms/user')->group(function () use ($callable) {

    Route::get('v1/me', function (Request $request) use ($callable) {

        return app()->call($callable.'AuthController@me');

    });

});
