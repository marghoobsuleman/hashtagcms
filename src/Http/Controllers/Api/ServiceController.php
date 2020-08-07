<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use MarghoobSuleman\HashtagCms\Core\DataLoader;
use MarghoobSuleman\HashtagCms\Core\ServiceLoader;
use MarghoobSuleman\HashtagCms\Models\SplashScreen;

use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;

class ServiceController extends ApiBaseController
{
    use FeEssential;

    protected $viewIndex = "_services_/index";

    /**
     * Get data for mobile splash screen
     * @queryParam $context Site context
     * @queryParam $tenant Tenant link rewrite
     * @param Request $request
     * @return array
     */
    public function splashScreen(Request $request) {
        $context = $request->get("context");
        $tenant = $request->get("tenant") ?? null;
        return SplashScreen::loadData($context, $tenant);
    }

    /**
     * Load data
     * @queryParam $lang language code
     * @queryParam $tenant Tenant link rewrite
     * @queryParam $category Category link rewrite or id
     * @param Request $request
     * @return array
     */
    public function loadData(Request $request) {

        $query = $request->query();

        $lang = $query["lang"];
        $tenant = $query["tenant"];
        $site = $query["site"];
        $category = $query["category"];
        $microsite = $query["microsite"] ?? 0;

        $loader = new DataLoader();
        return $loader->loadData($category, $lang, $tenant, $site, $microsite, false);
    }


    /**
     * Load Module
     * @queryParam $name - Module Alias
     * @queryParam $json - return type as json or html
     * @queryParam $lang - language code
     * @queryParam $tenant - tenant link rewrite - web|android
     * @queryParam $site - site context
     * @queryParam $category - category link rewrite
     * @queryParam $microsite - microsite id
     * @param Request $request
     * @return array|string
     */
    public function loadModule(Request $request) {

        $query = $request->query();

        $name = $query["name"];
        $asJson = (isset($query["json"]) && $query["json"] == "true") ? true : false;

        $lang = $query["lang"] ?? "en";
        $tenant = $query["tenant"] ?? "web";
        $site = $query["site"] ?? config("hashtagcms.context");
        $category = $query["category"] ?? "/";
        $microsite = $query["microsite"] ?? 0;

        $loader = new ServiceLoader();
        $baseInfo = $loader->getInitBaseInfo($category, $lang, $tenant, $site, $microsite);

        if($baseInfo["status"] == 404) {
            return response()->json($baseInfo, 404);
        }

        $resData = $loader->loadModuleByAlias($name, $asJson, $category, $lang, $tenant, $site, $microsite);


        if($asJson == true) {
            return $resData;
        } else {
            $data['html'] = $resData;
            $data['baseInfo'] = $baseInfo;

            $theme = $baseInfo["theme"]->toArray();
            return $this->viewMaster($theme, $this->viewIndex, $data);
        }
    }

    /**
     * @queryParam $name - Hook Alias
     * @queryParam $json - return type as json or html
     * @queryParam $lang - language code
     * @queryParam $tenant - tenant link rewrite - web|android
     * @queryParam $site - site context
     * @queryParam $category - category link rewrite
     * @queryParam $microsite - microsite id
     * @param Request $request
     * @return array|mixed|string
     */
    public function loadHook(Request $request) {

        $query = $request->query();

        $name = $query["name"];
        $asJson = (isset($query["json"]) && $query["json"] == "true") ? true : false;

        $lang = $query["lang"] ?? "en";
        $tenant = $query["tenant"] ?? "web";
        $site = $query["site"] ?? config("hashtagcms.context");
        $category = $query["category"] ?? "/";
        $microsite = $query["microsite"] ?? 0;

        $loader = new ServiceLoader();
        $baseInfo = $loader->getInitBaseInfo($category, $lang, $tenant, $site, $microsite);
        $resData = $loader->loadModulesByHookAlias($name, $asJson, $category, $lang, $tenant, $site, $microsite);

        if($asJson == true) {
            return $resData;
        } else {
            $data['html'] = $resData;
            $data['baseInfo'] = $baseInfo;

            $theme = $baseInfo["theme"]->toArray();

            return $this->viewMaster($theme, $this->viewIndex, $data);
        }
    }

}
