<?php
use Illuminate\Support\Facades\DB;
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

        $controller = ($controller === "") ? "dashboard" : $controller; //default page of admin

        $methodType = $request->method();

        $namespace = config("hashtagcms.namespace");

        $controller = ($controller == "") ? config("admin.cmsInfo.defaultPage") : $controller;

        $namespace = config("hashtagcms.namespace");
        $appNamespace = app()->getNamespace();
        //Hashtag Controller
        $callable = $namespace."Http\Controllers\\Admin\\".str_replace("-", "", Str::title($controller))."Controller";
        //App Controller
        $callableApp = $appNamespace."Http\Controllers\\Admin\\".str_replace("-", "", Str::title($controller))."Controller";

        $controllerName = class_exists($callableApp) ? $callableApp : $callable;

        if(class_exists($controllerName)) {

            $method = ($method === "" && $methodType === "GET") ? "index" : $method;

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
                info("method: ". $methodType. " ::: ". $callable. " ::: ". json_encode($values));
                return app()->call($callable, $values);

            } catch (Exception $e) {
                info("There is an error ".$e->getMessage());
                return $e->getMessage();
                //abort(404);
            }

        } else {
            abort(404);
        }

    })->middleware(["web", "auth:sanctum", "cmsModuleInfo", "cmsInterceptor"])->where('params', '^((?!assets/).)*?');
});

if (HashtagCms::isRoutesEnabled()) {

    Route::match(['get', 'post', 'delete'], '{all?}', function(Request $request, $all="/") {

        $infoLoader = app()->HashtagCms->infoLoader();

        $infoKeeper = $infoLoader->getInfoKeeper();

        $callable = $infoLoader->getAppCallable(); //Controller and method
        $values =  $infoLoader->getAppCallableValue(); //controller params
        try {

            if($callable!="") {
                return app()->call($callable, $values); //FrontendController@index, []
            } else {
                info("I don't know what to process...");
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    info("Could not connect to the database.  Please check your configuration. Error: ".$e->getMessage());
                    die("Could not connect to the database.  Please check your configuration. Error: ".$e->getMessage());
                }
                return "RouteError: I don't know what to process...";
            }

        } catch (Exception $exception) {
            //show everything in local env
            if (env("APP_ENV") !== "local") {
                abort($exception->getStatusCode(), $exception->getMessage());
            } else {
                return array(
                    "code" => $exception->getStatusCode() ?? " unknown",
                    "message" => $exception->getMessage(),
                    "controller" => "$callable"
                );
            }

        }

    })->where("all", HashtagCms::getIgnoredPath())->middleware(["web", "interceptor"]);

    //Keep some original routes
    Auth::routes();
}


