<?php

namespace MarghoobSuleman\HashtagCms\Core\Middleware\Traits;

use Illuminate\Support\Facades\Http;
use MarghoobSuleman\HashtagCms\Core\Main\SessionManager;
use MarghoobSuleman\HashtagCms\Core\Main\LayoutManager;
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
use MarghoobSuleman\HashtagCms\Core\Main\DataLoader;
use MarghoobSuleman\HashtagCms\Core\Utils\LayoutKeys;

trait BaseInfo {

    protected InfoLoader $infoLoader;
    protected SessionManager $sessionManager;
    protected LayoutManager $layoutManager;
    protected DataLoader $dataLoader;


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
        $this->layoutManager = app()->HashtagCms->layoutManager();
        $this->sessionManager = app()->HashtagCms->sessionManager();
        $this->dataLoader = app()->HashtagCms->dataLoader();

        $this->parsePath($request);

    }

    /**
     * @throws \ReflectionException
     */
    private function parsePath($request) {

        $isExternal = app()->HashtagCms->useExternalApi();
        $this->infoLoader->setInfoKeeper(LayoutKeys::IS_EXTERNAL, $isExternal);

        $clearCache = $request->get(LayoutKeys::CLEAR_CACHE_KEY) ?? false;


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

            //try to fetch by config or env variable CONTEXT
            if (empty($context)) {
                logger()->error("Unable to find context in config.");
                die("Unable to find context in config.");
            }
            $this->configData = $this->loadConfig($context, null, null, true);
            if ($this->configData == null) {
                logger()->error("was trying to loadConfig($context)");
                die("Unable to load config from api");
            }

        } else {
            //means using same db etc. need this line
            $siteData = $this->infoLoader->geSiteInfoByContextAndDomain($context, $domain, $fullDomain);
            if ($siteData == null) {
                logger()->error("was trying to load infoLoader->geSiteInfoByContextAndDomain($context, $domain, $fullDomain)");
                die("Site not found");
            }
            $context = $siteData->context;
            $this->configData = $this->loadConfig($context, null, null, false);
        }

        //check if there is an error
        if (isset($this->configData['status']) && $this->configData['status']!=200) {
            logger()->error($this->configData['message']);
            abort($this->configData['status'], $this->configData['message']);
        }
        //dd($this->configData);
        $defaultData = $this->configData['defaultData'];
        //platformData is only temp
        $platformList = isset($this->configData['platformData']) ? $this->configData['platformData'] : $this->configData['platforms'];
        $langList = isset($this->configData['lang']) ? $this->configData['lang'] : $this->configData['langs'];
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

        if ($path == "/" || $path == "") {
            //find default category
            //@todo: if site has large number of categories then it would have problem here. loop will be big. it should come from load config api
            $categoryData = $this->findData($categoryList, "id", $defaultData['categoryId']);
            //dd( $categoryData, $path, $defaultData['categoryId'], $categoryList);
            $categoryName = $categoryData['linkRewrite'];
        } else {
            if ($foundLang) {
                array_shift($path_arr);
            }
            if ($foundPlatform) {
                array_shift($path_arr);
            }

            $categoryName = join("/",$path_arr);
            $categoryName =  ($categoryName == "") ? "/" : $categoryName; //@todo:

        }

        //set controller info
        $this->setControllerInfo($path_arr, $foundLang, $foundPlatform);

        //set category info keeper
        $this->infoLoader->setInfoKeeper(LayoutKeys::CATEGORY_NAME, $categoryName);


        //set site info keeper
        $this->infoLoader->setInfoKeeper(LayoutKeys::CONTEXT, $siteData['context']);
        $this->infoLoader->setInfoKeeper(LayoutKeys::SITE_ID, $siteData["id"]);

        //set language info keeper
        $this->infoLoader->setInfoKeeper(LayoutKeys::FOUND_LANG, $foundLang);
        $this->infoLoader->setInfoKeeper(LayoutKeys::LANG_ID, $langData["id"]);
        $this->infoLoader->setInfoKeeper(LayoutKeys::LANG_ISO_CODE, $langData["isoCode"]);

        //set platform info keeper
        $this->infoLoader->setInfoKeeper(LayoutKeys::FOUND_PLATFORM, $foundPlatform);
        $this->infoLoader->setInfoKeeper(LayoutKeys::PLATFORM_ID, $platformData["id"]);
        $this->infoLoader->setInfoKeeper(LayoutKeys::PLATFORM_LINKREWRITE, $platformData['linkRewrite']);

        /**
         * Setting for global use
         * Set Context Vars:
         * int $category_id, int $site_id, int $platform_id, int $microsite_id=0
         */
        $microsite_id = 0;
        $this->infoLoader->setMultiContextVars($defaultData['categoryId'], $siteData["id"], $platformData["id"], $microsite_id);
        //Set Context Vars: Lang
        $this->infoLoader->setLanguageId($langData["id"]);


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

        $controllerName = LayoutKeys::DEFAULT_CONTROLLER_NAME;
        $methodName = LayoutKeys::DEFAULT_METHOD_NAME;

        $paramsValues = array();

        if ($pathLen === 0) {
            $categoryName = "/";
            //will handle
        } else if ($pathLen === 1) {
                $categoryName = $path_arr[0];
                $methodName = LayoutKeys::DEFAULT_METHOD_NAME;
        } else if ($pathLen > 1) {
            $categoryName = $path_arr[0];
            $methodName = $path_arr[1];

            if ($pathLen > 2) {
                $paramsValues = array_splice($path_arr, 2, $pathLen);
            }
        }

        $categoryName = ($categoryName === "") ? "/" : $categoryName;
        $controllerName = ($categoryName == "/") ? LayoutKeys::DEFAULT_CONTROLLER_NAME : $categoryName;


        $categoryInfo = $this->getCategoryData($categoryName);

        //   reality check for controller and method
        //	* if class exist controllerName (we have BlogController for blog category) else it’s frontend
        //	* if method exist (method name is story for BlogController) else it’s index;
        $callableData = $this->getControllerName($categoryInfo, $controllerName, $methodName);
        //dd("categoryName: $categoryName ", "controllerName: $controllerName, methodName: $methodName");
        $callable = $callableData[LayoutKeys::CONTROLLER_NAME];
        $methodName = $callableData[LayoutKeys::METHOD_NAME];
        $foundController = $callableData[LayoutKeys::FOUND_CONTROLLER];
        $foundMethod = $callableData[LayoutKeys::FOUND_METHOD];

        $this->infoLoader->setInfoKeeper(LayoutKeys::CONTROLLER_NAME, $callable);
        $this->infoLoader->setInfoKeeper(LayoutKeys::METHOD_NAME, $methodName);
        $this->infoLoader->setInfoKeeper(LayoutKeys::CATEGORY_NAME, $categoryName);

        // if controller is not found. $values will ie ["support", "tnc"]. if found $values will be ["tnc"]
        if(!$foundController && $categoryInfo != null) {
            array_unshift($paramsValues, $controllerName, $callableData[LayoutKeys::METHOD_NAME_PARAM]);
        }
        if($foundController && !$foundMethod) {
            array_unshift($paramsValues, $callableData[LayoutKeys::METHOD_NAME_PARAM]);
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

        $this->infoLoader->setInfoKeeper(LayoutKeys::CALLABLE_CONTROLLER, $callable."@".$methodName);
        $this->infoLoader->setInfoKeeper(LayoutKeys::CONTROLLER_VALUE, $values);


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
    private function getControllerName(array $categoryInfo=null, string $controller_name, string $method_name):array {

        if($categoryInfo !== null) {
            $controller_name = isset($categoryInfo['controllerName']) ? $categoryInfo['controllerName'] : str_replace("-", "", Str::title($controller_name))."Controller";
            info("----- Found category controller: ".$controller_name." ------");
        } else {
            $controller_name = str_replace("-", "", Str::title($controller_name))."Controller";
            info("----- Default controller: ".$controller_name." ------");
        }

        $namespace = config("hashtagcms.namespace");
        $appNamespace = app()->getNamespace();
        $callable = $namespace."Http\Controllers\\".$controller_name; //hashtag controller
        $callableDefault = $namespace."Http\Controllers\FrontendController"; //hashtag default controller
        $callableApp = $appNamespace."Http\Controllers\\".$controller_name; // app controller

        $finalCallableApp = class_exists($callableApp) ? $callableApp : $callable;

        $data = array();
        $foundController = false;
        $foundMethod = false;
        $this->infoLoader->setInfoKeeper(LayoutKeys::FOUND_CONTROLLER, false);
        $this->infoLoader->setInfoKeeper(LayoutKeys::FOUND_METHOD, false);
        $data[LayoutKeys::METHOD_NAME_PARAM] = $method_name;

        if(class_exists($finalCallableApp)) {
            $this->infoLoader->setInfoKeeper(LayoutKeys::FOUND_CONTROLLER, true);
            $foundController = true;
            $data[LayoutKeys::CONTROLLER_NAME] = $finalCallableApp;
            $data[LayoutKeys::METHOD_NAME] = LayoutKeys::DEFAULT_METHOD_NAME;
            if(method_exists($finalCallableApp, $method_name)) {
                $this->infoLoader->setInfoKeeper(LayoutKeys::FOUND_METHOD, true);
                $foundMethod = true;
                $data[LayoutKeys::METHOD_NAME] = $method_name;
            }
        } else {
            //default controller and method
            $data[LayoutKeys::CONTROLLER_NAME] = $callableDefault;
            $data[LayoutKeys::METHOD_NAME] = LayoutKeys::DEFAULT_METHOD_NAME;

        }

        $data[LayoutKeys::FOUND_CONTROLLER] = $foundController;
        $data[LayoutKeys::FOUND_METHOD] = $foundMethod;

        return $data;
    }


    /**
     * Load config
     * @param string $context
     * @param string|null $lang
     * @param string|null $platform
     * @param bool $isExternal
     * @return array|null
     */
    public function loadConfig(string $context, string $lang=null, string $platform=null, bool $isExternal):array|null {

        if ($isExternal) {
            return $this->dataLoader->loadConfigFromExternalApi($context, $lang, $platform);
        }

        return $this->dataLoader->loadConfig($context, $lang, $platform);
    }


    /**
     * Get category
     * @param string $categoryName
     * @return array|null
     */
    private function getCategoryData(string $categoryName) {
        $categoryList = $this->configData['categories'];
        return $this->findData($categoryList, 'linkRewrite', $categoryName);
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

