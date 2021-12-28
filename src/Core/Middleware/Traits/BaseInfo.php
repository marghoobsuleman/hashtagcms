<?php

namespace MarghoobSuleman\HashtagCms\Core\Middleware\Traits;

use MarghoobSuleman\HashtagCms\Core\Common;
use MarghoobSuleman\HashtagCms\Core\Main\CacheManager;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Site;
use Illuminate\Support\Facades\DB;

/**
 * Trait BaseInfo
 * @package MarghoobSuleman\HashtagCms\Http\Middleware\Core
 *
 * Following properties will be there inside app()->HashtagCmsInfoLoader->getInfoKeeper()
 * siteInfo =  {"id":1,"name":"CMS","context":"hashtagcms","favicon":"","title":"Welcome to #CMS"}
 *
 * tenantInfo: {"id":1, "name":"India","link_rewrite":"in","created_at":"2018-09-21 04:00:07","updated_at":"2018-09-21 04:00:07","deleted_at":null,"pivot":{"site_id":1,"tenant_id":1}}
 *
 * langInfo: {"id":1,"name":"English","iso_code":"en","language_code":"en","date_format_lite":"Y-m-d","date_format_full":"y-m-d H:i:s","is_rtl":0,"created_at":"2018-09-21 04:00:07","updated_at":"2018-09-21 04:00:07","deleted_at":null,"pivot":{"site_id":1,"lang_id":1}}
 *
 * controllerIndex = to check if tenant lang etc is there in url
 * controllerName = extracted from url pattern
 * controllerParams = ["about-us","contact"]
 * controllerParamsPath = "about-us\/contact"
 *
 */
use Illuminate\Support\Str;
use MarghoobSuleman\HashtagCms\Core\Main\InfoLoader;

trait BaseInfo {

    protected string $defaultController = "frontend";
    protected string $defaultMethod = "index";
    protected InfoLoader $infoLoader;
    protected CacheManager $cacheManager;

    /***
     * Web link could be
     * www.hashtagcms.org/en/web/home
     * en - language (optional)
     * web - tenant  (optional)
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
        $clearCache = $request->get('clearCache') ?? false;
        //Set Site Info
        //Set Lang Info
        //Set Tenant Info
        //Set Category Info
        //Set LinkRewrite Info

        //  path can be like this.
        //  https://www.hashtagcms.org/
        // or  https://www.hashtagcms.org/en/web/ or https://www.hashtagcms.org/en/web
        // or  https://www.hashtagcms.org/en/web/blog or https://www.hashtagcms.org/en/web/blog/test-story
        // or https://www.hashtagcms.org/blog or https://www.hashtagcms.org/blog/test-story
        // language and tenant can be in url. if it's not there; use default one.

        $path = $request->path();
        $path_arr = explode("/", $path); //example: en/web/blog/test-blog

        // #Set site info

        //Use local config for domain
        $domain = $request->getHost();
        $fullDomain = htcms_get_domain_path();

        //Fetching context based on domain from config. in case you want to run in local or ip
        $domainList = config("hashtagcms.domains");
        $context = $domainList[$domain] ?? "";

        $domainCacheKey = md5($domain);

        //use cache here
        try{

            if(!$this->cacheManager->exists($domainCacheKey) || $clearCache) {
                info("Fetching site info domain: $domain, or fetching context from config: $context");
                //noinspection ConstantConditions
                $siteInfo = $this->infoLoader->geSiteInfoByContextAndDomain($context, $domain, $fullDomain);
                //Stop everything if site info is not correct.
                if($siteInfo === null) {
                    info("Site not found!");
                    exit("Site has not been set up");
                }
                $this->cacheManager->put($domainCacheKey, $siteInfo);

            } else {
                info("From Cache ($domainCacheKey): Fetching site info domain: $domain, context: $context");
                $siteInfo = $this->cacheManager->get($domainCacheKey);
                //dd("siteInfo ",$siteInfo);
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }

        // ##Setting site info
        $siteInfo = $siteInfo->toArray();
        //Set Site Info
        $this->setSiteInfo($siteInfo);

        // #Set lang info
        $langCacheKey = md5($domain."_".$path_arr[0]."_lang");

        if(!$this->cacheManager->exists($langCacheKey) || $clearCache) {
            info("Fetching lang info: Path: $path_arr[0], lang_id: $siteInfo[lang_id]");
            //dd("Fetching lang info: Path: $path_arr[0], lang_id: $siteInfo[lang_id]");
            $langInfo = $this->infoLoader->getLangInfo($path_arr[0], $siteInfo['lang_id']); //index 0 is lang code or get the default lang id from site table
            //dd($path_arr[0], $siteInfo['lang_id'], $langInfo);
            //Stop everything if lang info is not correct.
            if($langInfo === null) {
                info("Lang not found!");
                exit("Language has not been set up");
            }
            $this->cacheManager->put($langCacheKey, $langInfo);

        } else {
            $langInfo = $this->cacheManager->get($langCacheKey);
            info("From Cache ($langCacheKey): Fetching tenant info: Path: $path_arr[0], lang_id: $siteInfo[lang_id]");
        }

        $langInfo = $langInfo->toArray();

        // ##Setting Language Info
        $this->setLanguageInfo($langInfo, $path_arr[0]);

        // #Set tenant info
        $tenantPlace = ($this->infoLoader->hasInInfoKeeper("foundLang") === true ) ? 1 : 0;
        $tenantCacheKey = md5($domain."_".$path_arr[$tenantPlace]."_tenant");

        if(!$this->cacheManager->exists($tenantCacheKey) || $clearCache) {
            info("Fetching tenant info: Path: $path_arr[$tenantPlace], tenant_id: $siteInfo[tenant_id]");
            $tenantInfo = $this->infoLoader->getTenantInfo($path_arr[$tenantPlace], $siteInfo['tenant_id']); //index 1 is tenant code or get the default tenant id from site table
            //dd($path_arr[1], $siteInfo['tenant_id'], $tenantInfo);
            //Stop everything if tenant info is not correct.
            if($tenantInfo === null) {
                info("tenant not found!");
                exit("Tenant has not been set up");
            }
            $this->cacheManager->put($tenantCacheKey, $tenantInfo);

        } else {
            $tenantInfo = $this->cacheManager->get($tenantCacheKey);
            info("From Cache ($tenantCacheKey): Fetching tenant info: Path: $path_arr[$tenantPlace], lang_id: $siteInfo[tenant_id]");
        }

        $tenantInfo = $tenantInfo->toArray();
        //dd("tenantInfo", $tenantInfo);
        // ##Setting Tenant Info
        $this->setTenantInfo($tenantInfo, $path_arr[$tenantPlace]);

        info("path array", $path_arr);

        $this->setControllerInfo($path, $request);

    }

    /**
     * Set site info
     * @param array $siteInfo
     */
    private function setSiteInfo(array $siteInfo) {
        //Site Info
        $siteInfoForRequest = array("id"=>$siteInfo["id"],
            "name"=>$siteInfo["name"],
            "context"=>$siteInfo["context"],
            "favicon"=>$siteInfo["favicon"],
            "category_id"=>$siteInfo["category_id"],
            "theme_id"=>$siteInfo["theme_id"],
            "tenant_id"=>$siteInfo["tenant_id"],
            "lang_id"=>$siteInfo["lang_id"],
            "country_id"=>$siteInfo["country_id"],
            "domain"=>$siteInfo["domain"],
            "lang_count"=>$siteInfo["lang_count"],
            "under_maintenance"=>$siteInfo["under_maintenance"]
        );

        if($siteInfo["under_maintenance"] === 1) {
            exit("Site is under under maintenance.");
            //@todo: set default controller controller
        }

        $this->infoLoader->setInfoKeeper("context", $siteInfo["context"]);
        $this->infoLoader->setInfoKeeper("siteId", $siteInfo["id"]);


        info("======================= setSiteInfo ================================");
        info("setSiteInfo:siteInfo ". json_encode($siteInfoForRequest));
        info("======================================================================");
        $this->infoLoader->setInfoKeeper("siteInfo", $siteInfoForRequest);

        //save in common too
        $this->infoLoader->setObjInfo('site', $siteInfo);

    }

    /**
     * Set language info
     * @param array $langInfo
     * @param string $iso_code
     */
    private function setLanguageInfo(array $langInfo, string $iso_code) {

        $this->infoLoader->setInfoKeeper("foundLang", false);
        if($langInfo['iso_code'] === $iso_code) {
            $this->infoLoader->setInfoKeeper("foundLang", true);
        }
        $this->infoLoader->setObjInfo('language', $langInfo);
        $this->infoLoader->setInfoKeeper("langId", $langInfo["id"]);
        $this->infoLoader->setInfoKeeper("langInfo", array("id"=>$langInfo["id"],
            "name"=>$langInfo["name"], "iso_code"=>$langInfo["iso_code"],
            "language_code"=>$langInfo["language_code"], "date_format_lite"=>$langInfo["date_format_lite"],
            "date_format_full"=>$langInfo["date_format_full"], "is_rtl"=>$langInfo["is_rtl"]));

        //Set locale
        app()->setLocale($langInfo["iso_code"]);
        info("======================= setLanguageInfo ================================");
        info("setLanguageInfo:langInfo ". json_encode($langInfo));
        info("====================================================================================");
    }


    /**
     * Set Tenant Info
     * @param array $tenantInfo
     * @param string $tenant_code
     */
    private function setTenantInfo(array $tenantInfo, string $tenant_code) {

        $this->infoLoader->setInfoKeeper("foundTenant", false);

        if($tenantInfo['link_rewrite'] === $tenant_code) {
            $this->infoLoader->setInfoKeeper("foundTenant", true);
        }

        $this->infoLoader->setObjInfo('tenant', $tenantInfo);
        $this->infoLoader->setInfoKeeper("tenantId", $tenantInfo["id"]);
        $this->infoLoader->setInfoKeeper("tenantInfo", array("id"=>$tenantInfo["id"],
            "name"=>$tenantInfo["name"], "link_rewrite"=>$tenantInfo["link_rewrite"]));

        //Set locale
        info("======================= setTenantInfo ================================");
        info("setTenantInfo:langInfo ". json_encode($tenantInfo));
        info("====================================================================================");

    }

    /**
     * Set category info
     * @param array $categoryInfo
     * @return void
     */
    public function setCategoryInfo(array $categoryInfo)
    {
        $this->infoLoader->setInfoKeeper("category_id", $categoryInfo['id']);
        $this->infoLoader->setInfoKeeper("category_link_rewrite", $categoryInfo['link_rewrite']);
        //save in common too
        $this->infoLoader->setObjInfo('categoryInfo', $categoryInfo);

        info("======================= setCategoryInfo ================================");
        info("setCategoryInfo:categoryInfo ". json_encode($categoryInfo));
        info("======================================================================");

    }

    /**
     * @param string $path
     * @param mixed $request
     * @throws \ReflectionException
     */

    private function setControllerInfo(string $path, mixed $request) {

        /*
         *** Too Much Url Handling
         ***
         *** Mess it with your own risk :).
         ***
        */

        info("============== Setting Controller Info ==============");

        $path_arr = ($path == "/") ? array("/") : explode("/", $path);
        $foundTenant = !($this->infoLoader->hasInInfoKeeper("foundTenant") === null) && $this->infoLoader->hasInInfoKeeper("foundTenant");
        $foundLang = !($this->infoLoader->hasInInfoKeeper("foundLang") === null) && $this->infoLoader->hasInInfoKeeper("foundLang");

        $fakeTenant = "fakeWeb";
        $fakeLang = "fakeEn";

        $controllerIndex = 2;
        $methodIndex = 3;

        //Normalize url

        if(!$foundLang) {
            array_unshift($path_arr, $fakeLang);
        }
        if(!$foundTenant) {
            array_unshift($path_arr, $fakeTenant);
        }
        //dd("path_arr ",$path_arr);
        // Controller and method is missing
        if(sizeof($path_arr) === 2) {
            array_push($path_arr, $this->defaultController, $this->defaultMethod);
        }

        //method is missing
        if(sizeof($path_arr) === 3) {
            $path_arr[] = $this->defaultMethod;
        }


        $clearCache = $request->get('clearCache') ?? false;
        $domain = $request->getHost();

        $categoryCacheKey = md5($domain."_".$path."_category");

        $categoryInfo = null;
        $site_id = $this->infoLoader->getInfoKeeper("siteId");

        if(!$this->cacheManager->exists($categoryCacheKey) || $clearCache) {
            info("Fetching category info : $path");
            $categoryInfo = $this->infoLoader->getCategoryInfo($path_arr[$controllerIndex], '', $site_id);
            if(!$foundTenant && !$foundLang) {
                $categoryInfo = $this->infoLoader->getCategoryInfo($path_arr[$controllerIndex], $path, $site_id); // load category of full url and controller
            }

            if($categoryInfo === null) {
                info("$path : Category not found!");
                // exit("$path : Category not found!");
            }
            if($categoryInfo !== null) {
                $this->cacheManager->put($categoryCacheKey, $categoryInfo);
            }

        } else {
            info("From Cache ($categoryCacheKey): Fetching category info ");
            $categoryInfo = $this->cacheManager->get($categoryCacheKey);
        }

        if($categoryInfo !== null) {
            //$categoryInfo = $categoryInfo->toArray();
            $this->setCategoryInfo($categoryInfo->toArray());
        }

        info("After making path array");
        info(json_encode($path_arr));

        //dd("path_arr", $path_arr);

        //make copies for some use
        $path_arr_copy = $path_arr;


        $controllerName = $path_arr[$controllerIndex];
        $methodName = $path_arr[$methodIndex];

        $controllerName = ($controllerName == "/") ? $this->defaultController : $controllerName;

        //  reality check for controller and method
        //	* if class exist controllerName (we have BlogController for blog category) else it’s frontend
        //	* if method exist (method name is story for BlogController) else it’s index;
        $callable = $this->getControllerName($categoryInfo, $controllerName);

        $foundController = false;
        $foundMethod = false;

        if(class_exists($callable)) {
            $foundController = true;
        }

        if(method_exists($callable, $methodName)) {

            //means it already has method | en/web/blog/story
            if(sizeof($path_arr) >= 4) {
                $foundMethod = true;
            }

        }

        //If controller found
        if($foundController) {

            //if method found
            if($foundMethod) {

                $linkRewrite = join("/", array_splice($path_arr_copy, $methodIndex+1, sizeof($path_arr_copy)-1));

            } else {

                $linkRewrite = join("/", array_splice($path_arr_copy, $methodIndex, sizeof($path_arr_copy)-1));
            }


        } else {

            $controllerName = $this->defaultController;

            if((sizeof($path_arr_copy) == 4 && $path_arr_copy[$methodIndex] == $this->defaultMethod)) {

                $linkRewrite = join("/", array_splice($path_arr_copy, $controllerIndex, 1));

            } else {

                $linkRewrite = join("/", array_splice($path_arr_copy, $controllerIndex, sizeof($path_arr_copy)-1));
            }

            $callable = $this->getControllerName($categoryInfo, $controllerName);

        }

        if(!$foundMethod) {
            $methodName = $this->defaultMethod;
        }

        $categoryName = ($path_arr[$controllerIndex] === $this->defaultController) ? "/" : join("/",array_splice($path_arr, $controllerIndex, sizeof($path_arr)-1));

        //dd("categoryName: $categoryName ", "controllerName: $controllerName, methodName: $methodName");

        $this->infoLoader->setInfoKeeper("controllerName", $controllerName);
        $this->infoLoader->setInfoKeeper("methodName", $methodName);
        $this->infoLoader->setInfoKeeper("categoryName", $categoryName);


        $linkRewrite = strtolower($linkRewrite);
        //$values = [];


        $values = explode('/', $linkRewrite);
        // exit(json_encode($values));

        $ref = new \ReflectionMethod($callable, $methodName);
        $params = $ref->getParameters();



        $args = [];

        foreach ($params as $param) {
            // parse signature [match, optional, type, name, default]
            preg_match('/<(required|optional)> (?:([\\\\a-z\d_]+) )?(?:\\$(\w+))(?: = (\S+))?/i', (string)$param, $matches);

            // assign untyped segments
            if ($matches[2] == null) {
                $args[$matches[3]] = array_shift($values);
            }
        }
        $values = array_merge($args, $values);


        //Check if controller and found and dynamic link option available ie: blog/{link_rewrite?}
        if($categoryInfo && !empty($categoryInfo->link_rewrite_pattern)) {

            $link_rewrite_pattern = $categoryInfo->link_rewrite_pattern;
            //Calculate required link count
            $totalCount = preg_match_all("/\{*+\}/", $link_rewrite_pattern, $matches);
            $optionalCount = preg_match_all("/\?}/", $link_rewrite_pattern, $matches);
            $requiredCount = $totalCount - $optionalCount;

            //size($value)-1 because it gives category link_rewrite in value array. Need only param. ie: blog/test-story. need only test-story
            if((sizeof($values)-1 > $totalCount) || sizeof($values)-1 < $requiredCount) {
                info("Dynamic url is mismatched");
                exit("Dynamic url is mismatched");
            }
            $valuesForContext = $values; //make a copy of values. ie.
            // if controller is not found. $values will ie ["support", "tnc"]. if found $values will be ["tnc"]
            if(!$foundController) {
                array_splice($valuesForContext, 0,1); //remove first index because it's a category. also explain in above lines
            }
            //dd('valuesForContext ',$valuesForContext);
            // Setting link_rewrite_patten keys with value in contextVars
            $link_rewrite_patterns = explode("/", $link_rewrite_pattern);
            foreach ($valuesForContext as $index=>$lr) {
                $key = preg_replace("/\{|\}|\?/", "", $link_rewrite_patterns[$index]);
                $this->infoLoader->setContextVars($key, $lr);
            }
            //dd("callable 1 ", $callable,$methodName, $values, $categoryInfo->link_rewrite_pattern);
        }

        $this->infoLoader->setInfoKeeper("callable", $callable."@".$methodName);
        $this->infoLoader->setInfoKeeper("callableValue", $values);


        $data = array("callable"=>$callable,
            "callableValue"=>$values,
            "controllerIndex"=>$controllerIndex,
            "controllerName"=> $controllerName,
            "categoryName"=> $categoryName,
            "method"=> $methodName
        );

        info("======================= setControllerInfo ================================");
        info("setControllerInfo: ". json_encode($data));
    }


    /**
     * Get controller name. If `App\Http\Controllers\{Controller}` exist, use that else use HashtagCms Controller
     * @param Category|null $categoryInfo
     * @param string $controller_name
     * @return string
     */
    private function getControllerName(?Category $categoryInfo, string $controller_name):string {

        if($categoryInfo !== null) {
            $controller_name = isset($categoryInfo->controller_name) ? $categoryInfo->controller_name : str_replace("-", "", Str::title($controller_name));
            info("----- Found category controller: ".$controller_name." ------");
        } else {
            $controller_name = str_replace("-", "", Str::title($controller_name));
            info("----- Default controller: ".$controller_name." ------");
        }

        $namespace = config("hashtagcms.namespace");
        $appNamespace = app()->getNamespace();
        $callable = $namespace."Http\Controllers\\".$controller_name."Controller";
        $callableApp = $appNamespace."Http\Controllers\\".$controller_name."Controller";

        return class_exists($callableApp) ? $callableApp : $callable;
    }


}

