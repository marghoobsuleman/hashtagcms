<?php

namespace MarghoobSuleman\HashtagCms\Core\Middleware\Traits;

use MarghoobSuleman\HashtagCms\Models\Site;

/**
 * Trait BaseInfo
 * @package MarghoobSuleman\HashtagCms\Http\Middleware\Core
 *
 * Following properties will be there inside request()->infoKeeper
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


trait BaseInfo {

    protected $defaultController = "frontend";
    protected $defaultMethod = "index";
    protected $common;

    protected $defaultCategory = "/";

    /***
     * Idea web link should be
     * www.hashtagcms.org/en/web/home
     *  en - language (optional)
     *  web - tenant  (optional)
     *  home - category
     */

    /**
     * @param $request
     * @return string
     */
    public function setBaseInfo($request) {

        info("BaseInfo: Start Processing...");
        $this->common = app()->Common;

        $path = $request->path();

        //set path in infoKeeper
        $this->pushToRequest("path", $path);

        //Use local config for domain
        $domain = $request->getHost();
        $fullDomain = htcms_get_domain_path();


        //Fetching context based on domain
        $domainList = config("hashtagcms.domains");
        $context = $domainList[$domain] ?? "";

        //use cache here
        try{
            info("Fetching site info domain: $domain, context: $context");
            $siteInfo =  Site::where("context", '=', $context)
                ->orWhere('domain', '=', $domain)
                ->orWhere('domain', '=', $fullDomain)
                ->with(['tenant', 'language'])->first();
        } catch (\Exception $e) {
            return $e->getMessage();
        }


        if($siteInfo == null || $siteInfo->count() == 0) {
            info("Site not found!");
            exit("Site has not been set up");
        } else {
            $path_arr = explode("/", $path);

            $siteInfo = $siteInfo->toArray();
            //Set Site Info
            $this->setSiteInfo($siteInfo);

            //Set Language Info
            $this->setLanguageInfo($path_arr, $siteInfo);

            //Set Tenant Info
            $this->setTenantInfo($path_arr, $siteInfo);

            //set controller index
            $this->setControllerInfo($path);

        }

    }

    /**
     * @param $siteInfo
     *
     */
    private function setSiteInfo(array $siteInfo) {
        //Site Info
        $siteInfoForRuquest = array("id"=>$siteInfo["id"],
            "name"=>$siteInfo["name"],
            "context"=>$siteInfo["context"],
            "favicon"=>$siteInfo["favicon"],
            "category_id"=>$siteInfo["category_id"],
            "theme_id"=>$siteInfo["theme_id"],
            "tenant_id"=>$siteInfo["tenant_id"],
            "lang_id"=>$siteInfo["lang_id"],
            "country_id"=>$siteInfo["country_id"],
            "domain"=>$siteInfo["domain"]
            );

        $this->pushToRequest("context", $siteInfo["context"]);
        $this->pushToRequest("siteId", $siteInfo["id"]);

        info("======================= setSiteInfo ================================");
        info("setSiteInfo:siteInfo ". json_encode($siteInfoForRuquest));
        $this->pushToRequest("siteInfo", $siteInfoForRuquest);

        //save in common too
        $this->common->setInfo('site', $siteInfo);

    }

    /**
     * Set Tenant Info
     * @param $request
     * @param $path_arr
     * @param $siteInfo
     */
    private function setTenantInfo(array $path_arr, $siteInfo) {

        $this->pushToRequest("foundTenant", FALSE);
        $this->pushToRequest("tenantInfo", NULL);

        $tenantPlace = ($this->hasInRequest("foundLang") === TRUE ) ? 1 : 0;
        //tenants
        $allTenants = $siteInfo["tenant"];

        $tenantIndex = findIndexInAssocArray($allTenants, "link_rewrite", $path_arr[$tenantPlace]);

        $this->common->setInfo('foundTenant', ($tenantIndex >=0 ) ? true : false);

        if($tenantIndex >= 0) {
            $this->pushToRequest("foundTenant", TRUE);
        }
        //default
        $tenantIndex = ($tenantIndex == -1) ? findIndexInAssocArray($allTenants, "id", $siteInfo["tenant_d"] ?? 1) : $tenantIndex;


        //Set Tenant
        if($tenantIndex >= 0) {

            $currentTenant = $allTenants[$tenantIndex];

            //dd("currentTenant", $currentTenant);

            $this->pushToRequest("tenantInfo", array("id"=>$currentTenant["id"],
                "name"=>$currentTenant["name"], "link_rewrite"=>$currentTenant["link_rewrite"]));

        }
        //Save in common too
        $this->common->setInfo('tenant', $allTenants[$tenantIndex==-1 ? 0 : $tenantIndex]);
        info("======================= setTenantInfo ================================");
        info("setTenantInfo:tenantInfo ". json_encode($currentTenant));

    }

    /**
     * @param $request
     * @param $path_arr
     * @param $siteInfo
     */
    private function setLanguageInfo(array $path_arr, $siteInfo) {
        $this->pushToRequest("foundLang", FALSE);
        $this->pushToRequest("langInfo", NULL);

        $languagePlace = 0;

        //info("path_arrSize: ". sizeof($path_arr) . " languagePlace: $languagePlace");

        $allLanguages = $siteInfo["language"];

        $langIndex = -1;
        if($languagePlace < sizeof($path_arr)) {

            //@todo: iso_code needs to be replaced with link_rewrite
            $langIndex = findIndexInAssocArray($allLanguages, "iso_code", $path_arr[$languagePlace]);

            if($langIndex >= 0) {
                $this->pushToRequest("foundLang", TRUE);
            }

        }
        $this->common->setInfo('foundLang', ($langIndex >= 0) ? true : false);


        $langIndex = ($langIndex == -1) ? findIndexInAssocArray($allLanguages, "id", $siteInfo["lang_id"] ?? 1) : $langIndex;

        //Set Language
        if($langIndex >= 0) {
            $currentLang = $allLanguages[$langIndex];
            $this->pushToRequest("langInfo", array("id"=>$currentLang["id"],
                "name"=>$currentLang["name"], "iso_code"=>$currentLang["iso_code"]));

            //Set locale
            app()->setLocale($currentLang["iso_code"]);
        }

        //Save in common
        $this->common->setInfo('language', $allLanguages[$langIndex==-1 ? 0 : $langIndex]);

        info("======================= setLanguageInfo ================================");
        info("setLanguageInfo:langInfo ". json_encode($currentLang));
    }


    /**
     * @param $path_arr
     * Set Controller Info
     * return void
     */

    private function setControllerInfo(string $path) {

        /*
         *** Too Much Url Handling
         ***
         *** Mess it with your own risk :).
         ***
        */

        info("============== Setting Controller Info ==============");

        $path_arr = ($path == "/") ? array("/") : explode("/", $path);

        $foundTenant = ($this->hasInRequest("foundTenant") == null) ? false : $this->hasInRequest("foundTenant");
        $foundLang = ($this->hasInRequest("foundLang") == null) ? false : $this->hasInRequest("foundLang");

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

        // Controller and method is missing
        if(sizeof($path_arr) == 2) {
            array_push($path_arr, $this->defaultController, $this->defaultMethod);
        }

        //method is missing
        if(sizeof($path_arr) == 3) {
            array_push($path_arr, $this->defaultMethod);
        }

        //Default category
        $categoryName = $this->defaultCategory;

        info("After making path array");
        info($path_arr);

        //dd("path_arr", $path_arr);

        //make copies for some use
        $path_arr_copy = $path_arr;


        $controllerName = $path_arr[$controllerIndex];
        $methodName = $path_arr[$methodIndex];

        $controllerName = ($controllerName == "/") ? $this->defaultController : $controllerName;

        //reality check for controller and method
        //	* if class exist controllername is blog else it’s frontend
        //	* if method exist method name is story else it’s index Str::ucfirst('foo bar');
        //$callable = $namespace."Http\Controllers\\".str_replace("-", "", Str::ucfirst($controllerName))."Controller";
        $callable = $this->getControllerName($controllerName);

        $foundController = false;
        $foundMethod = false;
        $category = null;

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

            //$callable = $namespace."Http\Controllers\\".str_replace("-", "", Str::title($controllerName))."Controller";
            $callable = $this->getControllerName($controllerName);

        }

        if(!$foundMethod) {
            $methodName = $this->defaultMethod;
        }

        $categoryName = ($path_arr[$controllerIndex] == $this->defaultController) ? "/" : join("/",array_splice($path_arr, $controllerIndex, sizeof($path_arr)-1));

        $this->pushToRequest("controllerName", $controllerName);
        $this->pushToRequest("methodName", $methodName);
        $this->pushToRequest("categoryName", $categoryName);


        $linkRewrite = strtolower($linkRewrite);
        $values = [];

        if($foundController) {
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
        }
        //dd($callable,$methodName);

        $this->pushToRequest("callable", $callable."@".$methodName);
        $this->pushToRequest("callableValue", $values);


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
     * Set in request
     * @param $request
     * @param $key
     * @param $value
     */
    public function pushToRequest($key, $value) {
        if(!isset($this->request->infoKeeper)) {
            $this->request->infoKeeper = array();
        }
        $this->request->infoKeeper[$key] = $value;
    }

    private function getFromRequest($key = NULL) {
        return ($key == NULL) ? $this->request->infoKeeper :  $this->request->infoKeeper[$key];
    }

    /**
     * Has in Request
     * @param $request
     * @param $key
     * @return bool
     */
    private function hasInRequest($key) {

        return isset($this->request->infoKeeper[$key]) ? $this->request->infoKeeper[$key] : NULL;
    }

    /**
     * Get controller name. If `App\Http\Controllers\{Controller}` exist, use that else use HashtagCms Controller
     * @param string $controller_name
     * @return string
     */
    private function getControllerName($controller_name='') {

        $namespace = config("hashtagcms.namespace");
        $appNamespace = app()->getNamespace();
        $callable = $namespace."Http\Controllers\\".str_replace("-", "", Str::title($controller_name))."Controller";
        $callableApp = $appNamespace."Http\Controllers\\".str_replace("-", "", Str::title($controller_name))."Controller";

        return class_exists($callableApp) ? $callableApp : $callable;
    }


}

