<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use MarghoobSuleman\HashtagCms\Core\Main\ServiceLoader;
use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;

class ServiceController extends ApiBaseController
{
    use FeEssential;

    /**
     * Get data for mobile splash screen
     * @param Request $request
     * @return array|string
     */
    public function siteConfigs(Request $request):array|string {
        $query = $request->all();
        $context = $query['site'];
        $lang = $query['lang'] ?? null;
        $tenant = $query['tenant'] ?? null;

        $api_secret = $query['api_secret'];
        $secrets = config("hashtagcms.api_secrets");
        $foundSecret = false;
        foreach ($secrets as $key=>$secret) {
            if($context === $key && $api_secret === $secret) {
                $foundSecret = true;
                break;
            }
        }
        if(!$foundSecret) {
            return response()->json(array("message"=>"Unauthorized access", "status"=>401), 401);
        }

        $loader = new ServiceLoader();
        return $loader->allConfigs($context, $lang, $tenant);

    }

    /**
     * Load data
     * @queryParam $lang language code
     * @queryParam $tenant Tenant link rewrite
     * @queryParam $category Category link rewrite or id
     * @param Request $request
     * @return array|string
     */
    public function loadData(Request $request):array|string {

        $query = $request->all();
        $loader = new ServiceLoader();

        $data = $loader->loadData($query);
        if($data["status"] != 200) {
            return response()->json($data, $data["status"]);
        }

        return $data;
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
    public function loadModule(Request $request): array|string
    {
        $query = $request->all();
        $loader = new ServiceLoader();
        $data = $loader->loadModule($query);


        if($data["status"] != 200) {
            return response()->json($data, $data["status"]);
        }
        if(!empty($query['json']) && (string)$query['json'] === "true") {
            return $data;
        }

        $layoutManager = app()->HashtagCms->layoutManager();
        $infoKeeper = app()->HashtagCmsInfoLoader->getInfoKeeper();
        $parsedData = $layoutManager->getParsedViewData((array)$data['module'], $infoKeeper);
        $withJs = $query['withJs'] ?? false;
        $withCss = $query['withCss'] ?? false;
        return view($layoutManager->getBaseServiceIndex(), array("data"=>$parsedData, "withCss"=>$withCss, "withJs"=>$withJs));

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
    public function loadHook(Request $request): array|string
    {

        $query = $request->all();
        $loader = new ServiceLoader();
        $data = $loader->loadHook($query);

        if($data["status"] != 200) {
            return response()->json($data, $data["status"]);
        }
        if(!empty($query['json']) && (string)$query['json'] === "true") {
            return $data;
        }
        $layoutManager = app()->HashtagCms->layoutManager();
        $infoKeeper = app()->HashtagCmsInfoLoader->getInfoKeeper();

        $parsedData = $layoutManager->getParsedViewDataFromMultipleModules($data['hook']['modules'], $infoKeeper);
        $withJs = $query['withJs'] ?? false;
        $withCss = $query['withCss'] ?? false;
        return view($layoutManager->getBaseServiceIndex(), array("data"=>$parsedData, "withCss"=>$withCss, "withJs"=>$withJs));

    }

}
