<?php

namespace MarghoobSuleman\HashtagCms\Core\Middleware\Traits;

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

trait BaseInfo {

    protected string $defaultController = "frontend";
    protected string $defaultMethod = "index";
    protected InfoLoader $infoLoader;
    protected CacheManager $cacheManager;

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
        $path_arr = explode("/", $path); //example: en/web/blog/test-blog
        $path_size = sizeof($path_arr);

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
            info("Site info error: ".$e->getMessage());
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
            info("From Cache ($langCacheKey): Fetching platform info: Path: $path_arr[0], lang_id: $siteInfo[lang_id]");
        }

        $langInfo = $langInfo->toArray();

        // ##Setting Language Info
        $this->setLanguageInfo($langInfo, $path_arr[0]);

        // #Set platform info
        $platformPlace = ($this->infoLoader->getInfoKeeper("foundLang") === true && $path_size > 1) ? 1 : 0;
        $platformCacheKey = md5($domain."_".$path_arr[$platformPlace]."_platform");

        if(!$this->cacheManager->exists($platformCacheKey) || $clearCache) {
            info("Fetching platform info: Path: $path_arr[$platformPlace], platform_id: $siteInfo[platform_id]");
            $platformInfo = $this->infoLoader->getPlatformInfo($path_arr[$platformPlace], $siteInfo['platform_id']); //index 1 is platform code or get the default platform id from site table
            //dd($path_arr[1], $siteInfo['platform_id'], $platformInfo);
            //Stop everything if platform info is not correct.
            if($platformInfo === null) {
                info("platform not found!");
                exit("Platform has not been set up");
            }
            $this->cacheManager->put($platformCacheKey, $platformInfo);

        } else {
            $platformInfo = $this->cacheManager->get($platformCacheKey);
            info("From Cache ($platformCacheKey): Fetching platform info: Path: $path_arr[$platformPlace], lang_id: $siteInfo[platform_id]");
        }

        $platformInfo = $platformInfo->toArray();
        //dd("platformInfo", $platformInfo);
        // ##Setting Platform Info
        $this->setPlatformInfo($platformInfo, $path_arr[$platformPlace]);

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
            "platform_id"=>$siteInfo["platform_id"],
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
        $this->infoLoader->setInfoKeeper("lang_iso_code", $langInfo["iso_code"]);
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
     * Set Platform Info
     * @param array $platformInfo
     * @param string $platform_code
     */
    private function setPlatformInfo(array $platformInfo, string $platform_code) {

        $this->infoLoader->setInfoKeeper("foundPlatform", false);

        if($platformInfo['link_rewrite'] === $platform_code) {
            $this->infoLoader->setInfoKeeper("foundPlatform", true);
        }

        $this->infoLoader->setObjInfo('platform', $platformInfo);
        $this->infoLoader->setInfoKeeper("platformId", $platformInfo["id"]);
        $this->infoLoader->setInfoKeeper("platform_link_rewrite", $platformInfo["link_rewrite"]);
        $this->infoLoader->setInfoKeeper("platformInfo", array("id"=>$platformInfo["id"],
            "name"=>$platformInfo["name"], "link_rewrite"=>$platformInfo["link_rewrite"]));

        //Set locale
        info("======================= setPlatformInfo ================================");
        info("setPlatformInfo:langInfo ". json_encode($platformInfo));
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
         *
         * Too Much Url Handling
         *
         *** Mess it with your own risk :).
         ***
        */

        $path_arr = explode("/", $path);
        $pathLen = sizeof($path_arr);

        $categoryName = "/";
        $methodName = "index";

        $foundLang = $this->infoLoader->getInfoKeeper("foundLang");
        $foundPlatform = $this->infoLoader->getInfoKeeper("foundPlatform");

        $paramsValues = array();

        if ($path === "/" || $pathLen===1) {
            if ($foundLang || $foundPlatform) {
                //$categoryName = "/";
            } else {
                $categoryName = $path;
            }
        } else if ($pathLen === 2) {
            if ($foundLang && $foundPlatform) {
                //found lang and platform = en/web
               // $categoryName = "/";
            } else if (!$foundLang && !$foundPlatform) {
                //no lang and platform fond in url = my/contact
                $categoryName = $path_arr[0];
                $methodName = $path_arr[1];
            } else {
                //either lang or platform is there in url = web/example | en/example
                $categoryName = $path_arr[1];
            }
        } else if ($pathLen === 3) {
            if ($foundLang && $foundPlatform) {
                //found lang and platform = en/web/example
                $categoryName = $path_arr[2];
            } else if(!$foundLang && !$foundPlatform) {
                //no lang and platform fond in url = my/contact/page
                $categoryName = $path_arr[0];
                $methodName = $path_arr[1];
                $paramsValues[] = $path_arr[2];
            } else {
                //either lang or platform is there in url = web/example/page | en/example/page
                $categoryName = $path_arr[1];
                $methodName = $path_arr[2];
            }
        } else if ($pathLen >= 4) {
            if ($foundLang && $foundPlatform) {
                //found lang and platform = en/web/example/page/extra/link
                $categoryName = $path_arr[2];
                $methodName = $path_arr[3];
                $paramsValues = array_splice($path_arr, 4, $pathLen);
            } else if(!$foundLang && !$foundPlatform) {
                //no lang and platform fond in url = anything/category/example/page
                $categoryName = $path_arr[0];
                $methodName = $path_arr[1];
                $paramsValues = array_splice($path_arr, 2, $pathLen);
            } else  {
                //either lang or platform is there in url = web/category/example/page | en/category/example/page
                $categoryName = $path_arr[1];
                $methodName = $path_arr[2];
                $paramsValues = array_splice($path_arr, 3, $pathLen);
            }
        }

        $fullPath = $path;

        if($foundLang) {
            $lang = $this->infoLoader->getInfoKeeper('lang_iso_code');
            $fullPath = str_replace($lang."/", "", $fullPath);
        }
        if($foundPlatform) {
            $platform = $this->infoLoader->getInfoKeeper('platform_link_rewrite');
            $fullPath = str_replace($platform."/", "", $fullPath);
        }

        //dd("foundLang: $foundLang, foundPlatform: $foundPlatform", "path: ".$path, "categoryName: ".$categoryName, "methodName: ".$methodName, "lang:".$lang, "platform: ".$platform, $params);

        info("============== Setting Controller Info ==============");

        $clearCache = $request->get('clearCache') ?? false;
        $domain = $request->getHost();

        $categoryCacheKey = md5($domain."_".$path."_category");

        $categoryInfo = null;
        $site_id = $this->infoLoader->getInfoKeeper("siteId");

        if(!$this->cacheManager->exists($categoryCacheKey) || $clearCache) {
            info("Fetching category info : $path");
            $categoryInfo = $this->infoLoader->getCategoryInfo($categoryName, $fullPath, $site_id);

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
            $categoryName = $categoryInfo->link_rewrite;
            $this->setCategoryInfo($categoryInfo->toArray());
        }
        //dd("foundLang: $foundLang, foundPlatform: $foundPlatform", "path: ".$path, "categoryName: ".$categoryName, "methodName: ".$methodName, "lang:".$lang, "platform: ".$platform, $categoryInfo);

        info(json_encode($path_arr));

        $controllerName = ($categoryName == "/") ? $this->defaultController : $categoryName;

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
            if ($matches[2] == null) {
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

            //dd("callable 1 ", $callable,$methodName, $values, $categoryInfo->link_rewrite_pattern);
        }
        //This is for global purpose if you need it somewhere
        $this->infoLoader->setInfoKeeper("__link_rewrite_pattern__", join("/", $values)); //This is for global use in future
        $this->infoLoader->setContextVars("__link_rewrite_pattern__", join("/", $values)); //This is for global use in future

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
    private function getControllerName(?Category $categoryInfo, string $controller_name, string $method_name):array {

        if($categoryInfo !== null) {
            $controller_name = isset($categoryInfo->controller_name) ? $categoryInfo->controller_name : str_replace("-", "", Str::title($controller_name));
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


}

