<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MarghoobSuleman\HashtagCms\Facades\HashtagCms;
use App\Http\Controllers\Controller;

if(HashtagCms::isInstallationRoutesEnabled()) {
    Route::get("/install",  config("hashtagcms.namespace")."Http\Controllers\Installer\InstallController@index");
    Route::post("/install/save",  config("hashtagcms.namespace")."Http\Controllers\Installer\InstallController@save");
}


//Register Admin
Route::prefix('admin')->group(function () {

    Route::match(['get', 'post', 'delete'],"{controller?}/{method?}/{params?}", function (Request $request, $controller="", $method="", $params=null) {

        $methodType = $request->method();

        //$namespace = app()->getNamespace();
        $namespace = config("hashtagcms.namespace");

        $controller = ($controller == "") ? config("admin.cmsInfo.defaultPage") : $controller;

        $namespace = config("hashtagcms.namespace");
        $appNamespace = app()->getNamespace();
        $callable = $namespace."Http\Controllers\\Admin\\".str_replace("-", "", Str::title($controller))."Controller";
        $callableApp = $appNamespace."Http\Controllers\\Admin\\".str_replace("-", "", Str::title($controller))."Controller";

        $controllerName = class_exists($callableApp) ? $callableApp : $callable;

        if(class_exists($controllerName)) {

            $method = ($method == "" && $methodType == "GET") ? "index" : $method;

            $callable = $controllerName . '@' . $method;
            $values     = explode('/', $params);
            $ref        = new ReflectionMethod($controllerName, $method);
            $params     = $ref->getParameters();
            $args       = [];

            foreach ($params as $param)
            {
                // parse signature [match, optional, type, name, default]
                preg_match('/<(required|optional)> (?:([\\\\a-z\d_]+) )?(?:\\$(\w+))(?: = (\S+))?/i', (string) $param, $matches);

                // assign untyped segments
                if($matches[2] == null)
                {
                    $args[$matches[3]] = array_shift($values);
                }
            }
            $values = array_merge($args, $values);

            try {
                //dd($callable, $values);
                return app()->call($callable, $values);

            } catch (Exception $e) {

                return $e->getMessage();
                //abort(404);
            }

        } else {
            abort(404);
        }

    })->middleware(["web", "auth", "crayonModuleInfo", "cmsInterceptor"])->where('params', '^((?!assets/).)*?');
}); //,

if (HashtagCms::isRoutesEnabled()) {
    Route::match(['get', 'post', 'delete'], '{all?}', function(Request $request, $all="/") {

        //These are coming from FeMiddleware-> Core/BaseInfo Trait
        $callable =  isset($request->infoKeeper["callable"]) ? $request->infoKeeper["callable"] : "";
        $values =  isset($request->infoKeeper["callableValue"]) ? $request->infoKeeper["callableValue"] : array();

        try {

            if($callable!="") {
                return app()->call($callable, $values);
            } else {
                info("RouteError: I don't know what to process...");
                return "RouteError: I don't know what to process...";
            }

        } catch (Exception $exception) {

            //show everything in local env
            if (env("APP_ENV") !== "local") {
                abort(404);
            }

            return array("popluated" => "Error in calling controller (".$callable.")",
                "errorMessage" => $exception->getMessage(),
                "errorTrace" =>     $exception->getTrace()
            );
        }

    })->where('all', '^((?!assets/)|(?!fonts/).)*?')->middleware(["web", "interceptor"]);

    //Keep some original routes
    Auth::routes();
}

