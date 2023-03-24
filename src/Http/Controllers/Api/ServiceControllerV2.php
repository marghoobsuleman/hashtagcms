<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use MarghoobSuleman\HashtagCms\Core\Main\ServiceLoaderV2 as ServiceLoader;
use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;
use Symfony\Component\HttpFoundation\Response;

class ServiceControllerV2 extends ApiBaseController
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
        $platform = $query['platform'] ?? null;

        //Basic level of api check -
        // site context and api secret should be there in config/hashtagcms.php
        $api_secret = $query['api_secret'] ?? null;
        if(empty($api_secret)) {
            return response()->json(array("message"=>"Api key is missing.", "status"=>Response::HTTP_BAD_REQUEST), Response::HTTP_BAD_REQUEST);
        }

        $secrets = config("hashtagcms.api_secrets");
        $foundSecret = false;
        foreach ($secrets as $key=>$secret) {
            if($context === $key && $api_secret === $secret) {
                $foundSecret = true;
                break;
            }
        }
        if(!$foundSecret) {
            return response()->json(array("message"=>"API key or site context is not valid", "status"=>Response::HTTP_BAD_REQUEST), Response::HTTP_BAD_REQUEST);
        }

        $loader = new ServiceLoader();

        try {
            $result = $loader->allConfigs($context, $lang, $platform);
            if (isset($result['status']) && $result['status']!=200) {
                return response()->json($result, $result['status'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $result;

    }

    /**
     * Load data
     * @queryParam $lang language code
     * @queryParam $platform Platform link rewrite
     * @queryParam $category Category link rewrite or id
     * @param Request $request
     * @return array|string
     */
    public function loadData(Request $request):array|string {

        $query = $request->all();
        $context = $query['site'];
        $lang = $query['lang'] ?? null;
        $platform = $query['platform'] ?? null;
        $category = $query['category'] ?? null;
        $microsite = $query['microsite'] ?? null;

        $loader = new ServiceLoader();
        try {
            $result = $loader->loadData($context, $lang, $platform, $category, $microsite);
            if (isset($result['status']) && $result['status']!=200) {
                return response()->json($result, $result['status'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $result;
    }


    /**
     * Load data mobile
     * @queryParam $lang language code
     * @queryParam $platform Platform link rewrite
     * @queryParam $category Category link rewrite or id
     * @param Request $request
     * @return array|string
     */
    public function loadDataMobile(Request $request):array|string {

        $query = $request->all();
        $context = $query['site'];
        $lang = $query['lang'] ?? null;
        $platform = $query['platform'] ?? null;
        $category = $query['category'] ?? null;
        $microsite = $query['microsite'] ?? null;

        $loader = new ServiceLoader();
        try {
            $result = $loader->loadData($context, $lang, $platform, $category, $microsite);
            if (isset($result['status']) && $result['status']!=200) {
                return response()->json($result, $result['status'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        unset($result['html']);
        return $result;
    }


    /**
     * Load Module
     * @queryParam $name - Module Alias
     * @queryParam $json - return type as json or html
     * @queryParam $lang - language code
     * @queryParam $platform - platform link rewrite - web|android
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
     * @queryParam $platform - platform link rewrite - web|android
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
