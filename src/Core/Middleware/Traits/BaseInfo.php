<?php

namespace MarghoobSuleman\HashtagCms\Core\Middleware\Traits;

use Illuminate\Support\Facades\Http;
use MarghoobSuleman\HashtagCms\Core\Main\CacheManager;
use MarghoobSuleman\HashtagCms\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 * Trait BaseInfo
 * @package MarghoobSuleman\HashtagCms\Http\Middleware\Core
 *
 * Following properties will be there inside app()->HashtagCmsInfoLoader->getInfoKeeper()
 * siteInfo =  {"id":1,"name":"CMS","context":"hashtagcms","favicon":"","title":"Welcome to #CMS"}
 *
 * platformInfo: {"id":1, "name":"India","link_rewrite":"in","created_at":"2018-09-21 04:00:07","updated_at":"2018-09-21 04:00:07","deleted_at":null,"pivot":{"site_id":1,"platform_id":1}}
 *
 * langInfo: {"id":1,"name":"English","iso_code":"en","language_code":"en","date_format_lite":"Y-m-d","date_format_full":"y-m-d H:i:s","is_rtl":0,"created_at":"2018-09-21 04:00:07","updated_at":"2018-09-21 04:00:07","deleted_at":null,"pivot":{"site_id":1,"lang_id":1}}
 *
 * controllerIndex = to check if platform lang etc is there in url
 * controllerName = extracted from url pattern
 * controllerParams = ["about-us","contact"]
 * controllerParamsPath = "about-us\/contact"
 *
 */
use Illuminate\Support\Str;
use MarghoobSuleman\HashtagCms\Core\Main\InfoLoader;

use MarghoobSuleman\HashtagCms\Core\Main\DataLoader as DataLoader;

trait BaseInfo {

    protected string $defaultController = "frontend";
    protected string $defaultMethod = "index";
    protected InfoLoader $infoLoader;
    protected CacheManager $cacheManager;


    protected $configData;


    /***
     * Web link could be
     * www.hashtagcms.org/en/web/home
     * en - language (optional)
     * web - platform  (optional)
     * home or / - category
     */

    /**
     * @param $request
     * @return void
     * @throws \ReflectionException
     */
    public function setBaseInfo($request)
    {

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. Error: ".$e->getMessage());
        }

        info("BaseInfo: Start Processing...");


        // ******************** new one ********************* //
        $this->infoLoader = app()->HashtagCms->infoLoader();
        $this->cacheManager = app()->HashtagCms->cacheManager();

        $this->parsePath($request);

    }

    /**
     * @throws \ReflectionException
     */
    private function parsePath($request) {

        $isExternal = env('HASHTAGCMS_ENABLE_API')===true;

        if ($isExternal) {
            info("load from api");
            $apiUrl = env('HASHTAGCMS_API_SOURCE');
        }
        info("externalApi: $isExternal ");

        $clearCache = $request->get('clearCache') ?? false;


        //Set Site Info
        //Set Lang Info
        //Set Platform Info
        //Set Category Info
        //Set LinkRewrite Info

        //  path can be like this.
        //  https://www.hashtagcms.org/
        // or  https://www.hashtagcms.org/en/web/ or https://www.hashtagcms.org/en/web
        // or  https://www.hashtagcms.org/en/web/blog or https://www.hashtagcms.org/en/web/blog/test-story
        // or https://www.hashtagcms.org/blog or https://www.hashtagcms.org/blog/test-story
        // language and platform can be in url. if it's not there; use default one.

        $path = $request->path();
        $path = str_replace("//", "/", $path); //incase of double slash
        $path_arr = explode("/", $path); //example: en/web/blog/test-blog
        $path_size = sizeof($path_arr);

        // #Set site info

        //Use local config for domain
        $domain = $request->getHost();
        $fullDomain = htcms_get_domain_path();

        //Fetching context based on domain from config. in case you want to run in local or ip
        $domainList = config("hashtagcms.domains");
        $context = $domainList[$domain] ?? "";

        //will check external load api
        if ($isExternal) {
            //$this->configData = $this->loadConfig($context, null, null, true);

            //try to fetch by config or env variable CONTEXT
            if (empty($context)) {
                die("Please set the context first");
            }

        } else {
            //means using same db etc. need this line
            $siteData = $this->infoLoader->geSiteInfoByContextAndDomain($context, $domain, $fullDomain);
            if ($siteData == null) {
                die("Site not found");
            }
            $context = $siteData->context;
        }
        $this->configData = $this->loadConfig($siteData->context, null, null, false);

        //check if there is an error
        if (isset($this->configData['status']) && $this->configData['status']!=200) {
            logger()->error($this->configData['message']);
            abort($this->configData['status'], $this->configData['message']);
        }

        $defaultData = $this->configData['defaultData'];
        $platformList = $this->configData['platforms'];
        $langList = $this->configData['langs'];
        $categoryList = $this->configData['categories'];

        $defaultLang = $this->findData($langList, "id", $defaultData['langId']);
        $defaultPlatform = $this->findData($platformList, "id", $defaultData['platformId']);
        $defaultCategory = $this->findData($categoryList, "id", $defaultData['categoryId']);

        //dd($defaultLang, $defaultPlatform, $defaultCategory);

        // Setting site info
        $siteData = $this->configData['site'];

        //find lang
        $langData = $this->findData($langList, "isoCode", $path_arr[0]);
        $foundLang = ($langData != null);

        //find platform
        $platformIndex = ($foundLang === true && $path_size > 1) ? 1 : 0;
        $platformData = $this->findData($platformList, "linkRewrite", $path_arr[$platformIndex]);
        $foundPlatform = ($platformData != null);

        //set default if lang not found
        $langData = ($foundLang) ? $langData : $this->findData($langList, "id", $defaultData['langId']);
        $platformData = ($foundPlatform) ? $platformData : $this->findData($platformList, "id", $defaultData['platformId']);

        //find category
        if ($path == "/" || $path == "") {
            //if site has large number of categories then it would have problem here. loop will be big. will refactor this
            $categoryData = $this->findData($categoryList, "id", $defaultData['categoryId']);
            $categoryName = $categoryData['linkRewrite'];
        } else {
            if ($foundLang) {
                array_shift($path_arr);
            }
            if ($foundPlatform) {
                array_shift($path_arr);
            }
            $categoryName = join("/",$path_arr);
            $categoryName = ($categoryName === "") ? "/" : $categoryName;
        }
        //load data now
        $allData =  $this->loadData($siteData['context'], $langData['isoCode'], $platformData['linkRewrite'], $categoryName, null, $isExternal);


        //check if there is an error
        if (isset($allData['status']) && $allData['status']!=200) {
            logger()->error("Was trying to load category: $categoryName");
            logger()->error($allData['message']);
            abort($allData['status'], $allData['message']);
        }

        //Set everything; this has to come before setting the controller info
        $this->infoLoader->setLoaloadDataObjectAndEverything($allData);

        $this->setControllerInfo($path_arr, $foundLang, $foundPlatform);

        //dd($siteData, $langData, $platformData, $categoryData ?? null, $foundLang, $foundPlatform);


    }

    /**
     * @param mixed $request
     * @param string $path
     * @return void
     * @throws \ReflectionException
     */

    private function setControllerInfo(array $path, bool $foundLang, bool $foundPlatform) {

        /*
         *
         * Too Much Url Handling
         *
         *** Mess it with your own risk :).
         ***
        */

        $path_arr = $path;
        $pathLen = sizeof($path_arr);

        $controllerName = $this->defaultController;
        $methodName = $this->defaultMethod;

        $paramsValues = array();

        if ($pathLen === 0) {
            $categoryName = "/";
            //will handle
        } else if ($pathLen === 1) {
                $categoryName = $path_arr[0];
                $methodName = $this->defaultMethod;
        } else if ($pathLen > 1) {
            $categoryName = $path_arr[0];
            $methodName = $path_arr[1];

            if ($pathLen > 2) {
                $paramsValues = array_splice($path_arr, 2, $pathLen);
            }
        }

        $categoryName = ($categoryName === "") ? "/" : $categoryName;
        $controllerName = ($categoryName == "/") ? $this->defaultController : $categoryName;

        $categoryInfo = $this->infoLoader->getCategoryData();

        //   reality check for controller and method
        //	* if class exist controllerName (we have BlogController for blog category) else it’s frontend
        //	* if method exist (method name is story for BlogController) else it’s index;
        $callableData = $this->getControllerName($categoryInfo, $controllerName, $methodName);
        //dd("categoryName: $categoryName ", "controllerName: $controllerName, methodName: $methodName");
        $callable = $callableData['controllerName'];
        $methodName = $callableData['methodName'];
        $foundController = $callableData['foundController'];
        $foundMethod = $callableData['foundMethod'];

        $this->infoLoader->setInfoKeeper("controllerName", $callable);
        $this->infoLoader->setInfoKeeper("methodName",$methodName );
        $this->infoLoader->setInfoKeeper("categoryName", $categoryName);

        // if controller is not found. $values will ie ["support", "tnc"]. if found $values will be ["tnc"]
        if(!$foundController && $categoryInfo != null) {
            array_unshift($paramsValues, $controllerName, $callableData['methodNameParam']);
        }
        if($foundController && !$foundMethod) {
            array_unshift($paramsValues, $callableData['methodNameParam']);
        }

        $ref = new \ReflectionMethod($callable, $methodName);
        $params = $ref->getParameters();

        $args = [];

        foreach ($params as $param) {
            // parse signature [match, optional, type, name, default]
            preg_match('/<(required|optional)> (?:([\\\\a-z\d_]+) )?(?:\\$(\w+))(?: = (\S+))?/i', (string)$param, $matches);

            // assign untyped segments
            if ($matches[2] == null || $matches[2] == "") {
                $args[$matches[3]] = array_shift($paramsValues);
            }
        }

        $values = array_merge($args, $paramsValues);


        //Check if controller and found and dynamic link option available ie: blog/{link_rewrite?}
        if($categoryInfo && !empty($categoryInfo->link_rewrite_pattern)) {

            $link_rewrite_pattern = $categoryInfo->link_rewrite_pattern;
            //Calculate required link count
            $totalCount = preg_match_all("/\{*+\}/", $link_rewrite_pattern, $matches);
            $optionalCount = preg_match_all("/\?}/", $link_rewrite_pattern, $matches);
            $requiredCount = $totalCount - $optionalCount;

            //size($value)-1 because it gives category link_rewrite in value array. Need only param. ie: blog/test-story. need only test-story
            if($requiredCount !== 0 && $requiredCount < sizeof($values)-1) {
                info("Dynamic url is mismatched");
                exit("Dynamic url is mismatched");
            }

            $valuesForContext = $values; //make a copy of values. ie.
            // if controller is not found. $values will ie ["support", "tnc"]. if found $values will be ["tnc"]
            if(!$foundController) {
                array_splice($valuesForContext, 0,1); //remove first index because it's a category. also explain in above lines
            }
            //dd('valuesForContext: ',$valuesForContext);
            // Setting link_rewrite_patten keys with value in contextVars
             $link_rewrite_patterns = explode("/", $link_rewrite_pattern); //make array
            //dd($link_rewrite_patterns, $values);
            if(sizeof($link_rewrite_patterns) === sizeof($values)) {
                foreach ($valuesForContext as $index=>$lr) {
                    $key = preg_replace("/\{|\}|\?/", "", $link_rewrite_patterns[$index]);
                    $this->infoLoader->setContextVars($key, $lr);
                }
            } else {
                $key = preg_replace("/\{|\}|\?/", "", $link_rewrite_pattern);
                $this->infoLoader->setContextVars($key, join("/", $valuesForContext));
            }

            //dd("callable 1 ", $callable,$methodName, $values, $categoryInfo['link_rewrite_pattern']);
        }

        $this->infoLoader->setInfoKeeper("callable", $callable."@".$methodName);
        $this->infoLoader->setInfoKeeper("callableValue", $values);


        $data = array("callable"=>$callable,
            "callableValue"=>$values,
            "controllerName"=> $controllerName,
            "categoryName"=> $categoryName,
            "method"=> $methodName
        );

        //dd($data);

        info("======================= setControllerInfo ================================");
        info("setControllerInfo: ". json_encode($data));
    }


    /**
     * Get controller name. If `App\Http\Controllers\{Controller}` exist, use that else use HashtagCms Controller
     * @param Category|null $categoryInfo
     * @param string $controller_name
     * @param string $method_name
     * @return array
     */
    private function getControllerName(array $categoryInfo, string $controller_name, string $method_name):array {

        if($categoryInfo !== null) {
            $controller_name = isset($categoryInfo['controller_name']) ? $categoryInfo['controller_name'] : str_replace("-", "", Str::title($controller_name));
            info("----- Found category controller: ".$controller_name." ------");
        } else {
            $controller_name = str_replace("-", "", Str::title($controller_name));
            info("----- Default controller: ".$controller_name." ------");
        }

        $namespace = config("hashtagcms.namespace");
        $appNamespace = app()->getNamespace();
        $callable = $namespace."Http\Controllers\\".$controller_name."Controller"; //hashtag controller
        $callableDefault = $namespace."Http\Controllers\FrontendController"; //hashtag default controller
        $callableApp = $appNamespace."Http\Controllers\\".$controller_name."Controller"; // app controller

        $finalCallableApp = class_exists($callableApp) ? $callableApp : $callable;

        $data = array();
        $foundController = false;
        $foundMethod = false;
        $this->infoLoader->setInfoKeeper("foundController", false);
        $this->infoLoader->setInfoKeeper("foundMethod", false);
        $data['methodNameParam'] = $method_name;

        if(class_exists($finalCallableApp)) {
            $this->infoLoader->setInfoKeeper("foundController", true);
            $foundController = true;
            $data['controllerName'] = $finalCallableApp;
            $data['methodName'] = $this->defaultMethod;
            if(method_exists($finalCallableApp, $method_name)) {
                $this->infoLoader->setInfoKeeper("foundMethod", true);
                $foundMethod = true;
                $data['methodName'] = $method_name;
            }
        } else {
            //default controller and method
            $data['controllerName'] = $callableDefault;
            $data['methodName'] = $this->defaultMethod;

        }
        $data['foundController'] = $foundController;
        $data['foundMethod'] = $foundMethod;

        return $data;
    }


    /**
     * Load config
     * @param string $context
     * @param string|null $lang
     * @param string|null $platform
     * @param bool $isExternal
     * @return array|string
     */
    public function loadConfig(string $context, string $lang=null, string $platform=null, bool $isExternal):array {
        if ($isExternal) {
            $apiUrl = env('HASHTAGCMS_API_SOURCE');
            Http::get($apiUrl."?site={$context}");
            return "from api";
        }
        $dataLoader = new DataLoader();
        $data = $dataLoader->loadConfig($context, $lang, $platform);
        return $data;
    }

    /**
     * @param string $context
     * @param string $lang
     * @param string $platform
     * @param string $category
     * @param string $microsite
     * @return array|string
     */
    public function loadData(string $context, string $lang=null, string $platform=null, string $category=null, string $microsite=null, bool $isExternal):array {
        if ($isExternal) {
            $apiUrl = env('HASHTAGCMS_API_SOURCE');
            Http::get($apiUrl."?site={$context}");
            return "from api";
        }
        $dataLoader = new DataLoader();
        $data = $dataLoader->loadData($context, $lang, $platform, $category, $microsite);
        return $data;
    }


    /**
     * @param array $arr
     * @param string $key
     * @param mixed $val
     * @return array|null
     */
    protected function findData(array $arr, string $key, mixed $val):null|array {
        for ($i=0;$i<sizeof($arr);$i++) {
            $current = $arr[$i];
            if ($current[$key] === $val) {
                return $current;
            }
        }
        return null;
    }





}

