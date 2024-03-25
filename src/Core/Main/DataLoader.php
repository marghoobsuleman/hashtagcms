<?php

namespace MarghoobSuleman\HashtagCms\Core\Main;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MarghoobSuleman\HashtagCms\Core\Traits\LayoutHandler;
use MarghoobSuleman\HashtagCms\Http\Resources\CategoryResource;
/** Models */
use MarghoobSuleman\HashtagCms\Http\Resources\CategorySiteResource;
use MarghoobSuleman\HashtagCms\Http\Resources\CountryResource;
use MarghoobSuleman\HashtagCms\Http\Resources\CurrencyResource;
use MarghoobSuleman\HashtagCms\Http\Resources\FestivalResource;
use MarghoobSuleman\HashtagCms\Http\Resources\HookResource;
use MarghoobSuleman\HashtagCms\Http\Resources\LangResource;
use MarghoobSuleman\HashtagCms\Http\Resources\ModuleResource;
use MarghoobSuleman\HashtagCms\Http\Resources\PlatformResource;
use MarghoobSuleman\HashtagCms\Http\Resources\SitePropResource;
use MarghoobSuleman\HashtagCms\Http\Resources\SiteResource;
/** Resources */
use MarghoobSuleman\HashtagCms\Http\Resources\ThemeResource;
use MarghoobSuleman\HashtagCms\Http\Resources\ZoneResource;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\CategorySite;
use MarghoobSuleman\HashtagCms\Models\Hook;
use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Module;
use MarghoobSuleman\HashtagCms\Models\ModuleSite;
use MarghoobSuleman\HashtagCms\Models\Platform;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\SiteProp;
use MarghoobSuleman\HashtagCms\Models\Theme;
/** Traits */
use Symfony\Component\HttpFoundation\Response;

class DataLoader
{
    use LayoutHandler;

    private InfoLoader $infoLoader;

    private ModuleLoader $moduleLoader;

    public function __construct()
    {
        $this->infoLoader = app()->HashtagCms->infoLoader();
        $this->moduleLoader = app()->HashtagCms->moduleLoader();
    }

    /**
     * Load config
     */
    public function loadConfig(string $context, ?string $lang = null, ?string $platform = null): array
    {

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            logger()->error('DataLoader->loadConfig: Database Error: '.$e->getMessage());

            return $this->getErrorMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // fetch site info from request -> context
        // fetch lang info from request -> lang
        // fetch platform info from request -> platform
        // if lang is passed in request use that else fetch lang_id from site data
        // if platform is passed in request use that else fetch platform_id from site data
        // after that fetch all the info.
        if (empty($context)) {
            logger()->error('ServiceLoader>allConfigs: Site context is missing');

            return $this->getErrorMessage('Site context is missing', Response::HTTP_BAD_REQUEST);
        }

        $oldSiteId = htcms_get_siteId_for_admin();
        $oldLangId = htcms_get_language_id_for_admin();

        $lang_id = null;
        $platform_id = null;
        $siteData = Site::where('context', '=', $context)->first();

        //Send error
        if (empty($siteData)) {
            logger()->error('ServiceLoader>allConfigs: Site not found');

            return $this->getErrorMessage('Site not found', Response::HTTP_NOT_FOUND);
        }

        //if lang param is not empty fetch the lang info.
        // If found use that else use default lang from the site
        if (! empty($lang)) {
            $langData = Lang::where('iso_code', '=', $lang)->first();
            if (! empty($langData)) {
                $lang_id = $langData->id;
            }
        }

        //if platform param is not empty fetch form the db
        if (! empty($platform)) {
            $platformData = Platform::where('link_rewrite', '=', $platform)->first();
            if (! empty($platformData)) {
                $platform_id = $platformData->id;
            }
        }

        //set default
        $site_id = $siteData->id;
        $lang_id = ($lang_id == null) ? $siteData->lang_id : $lang_id; //set default lang if param is not passed
        $platform_id = ($platform_id == null) ? $siteData->platform_id : $platform_id; //set default platform if param is not passed

        /** set scope lang id */
        htcms_set_language_id_for_admin($lang_id);

        //Start fetching everything now
        $siteData = Site::with('lang')->where('context', $context)->first();
        $propsData = SiteProp::where([['site_id', '=', $site_id], ['platform_id', '=', $platform_id]])->get();

        //defaults
        $defaultData['categoryId'] = $siteData->category_id;
        $defaultData['themeId'] = $siteData->theme_id;
        $defaultData['platformId'] = $siteData->platform_id;
        $defaultData['langId'] = $siteData->lang_id;
        $defaultData['countryId'] = $siteData->country_id;
        $defaultData['currencyId'] = $siteData->currency_id;
        $defaultData['category'] = (new CategoryResource(Category::find($siteData->category_id)))->toArray(request());

        //convert to resource
        $siteInfo = (new SiteResource($siteData))->toArray(request());
        $platformsInfo = PlatformResource::collection($siteData->platform)->toArray(request());
        $langsInfo = LangResource::collection($siteData->language)->toArray(request());
        $currenciesInfo = CurrencyResource::collection($siteData->currency)->toArray(request());
        $zonesInfo = ZoneResource::collection($siteData->zone)->toArray(request());
        $countriesInfo = CountryResource::collection($siteData->country)->toArray(request());
        $categoriesInfo = CategoryResource::collection($siteData->categoryLang)->toArray(request());
        $propsInfo = SitePropResource::collection($propsData)->toArray(request());
        $festivalInfo = FestivalResource::collection($siteData->festival)->toArray(request());

        $data['site'] = $siteInfo;
        $data['defaultData'] = $defaultData;
        $data['platforms'] = $platformsInfo;
        $data['langs'] = $langsInfo;
        $data['currencies'] = $currenciesInfo;
        $data['zones'] = $zonesInfo;
        $data['countries'] = $countriesInfo;
        $data['categories'] = $categoriesInfo;
        $data['props'] = $propsInfo;
        if (! empty($festivalInfo)) {
            $data['festivals'] = $festivalInfo;
        }

        /**
         * reset scopes
         */
        htcms_set_siteId_for_admin($oldSiteId);
        htcms_set_language_id_for_admin($oldLangId);

        return $data;
    }

    /**
     * Load config data from external api
     *
     * @return array|mixed
     */
    public function loadConfigFromExternalApi(string $context, ?string $lang = null, ?string $platform = null)
    {
        try {
            $apiUrl = app()->HashtagCms->getConfigApiSource();

            $apiSecretAndContext = $this->getApiKeyAndContext($apiUrl);

            if ($apiSecretAndContext['apiSecret'] == null) {
                dd('Unable to find api secret key in config');
            }

            $apiSecret = $apiSecretAndContext['apiSecret'];
            $context = $apiSecretAndContext['context'];

            $apiUrl = $apiUrl."?site=$context&api_secret=$apiSecret";
            $headers['Content-Type'] = 'application/json';
            $headers['api_key'] = $apiSecret;

            $http = Http::withHeaders($headers)->get($apiUrl);

            if ($http->status() == 200) {
                $data = $http->json();
            } else {
                $msg = $http->reason()." :from API: $apiUrl";
                logger()->error($msg);
                throw new \Exception($msg, $http->status());
            }

        } catch (\Exception $exception) {
            $msg = 'Error while loading config: '.$exception->getMessage();
            logger()->error($msg);
            $data = ['status' => Response::HTTP_PRECONDITION_FAILED, 'message' => $msg];
        }

        return $data;
    }

    /**
     * Load data
     */
    public function loadData(string $context, ?string $lang = null, ?string $platform = null, ?string $category = null, ?string $microsite = null): array
    {
        info("loading started for: $category, context: $context, platform: $platform lang: $lang");
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            logger()->error('DataLoader->loadData: Database Error: '.$e->getMessage());

            return $this->getErrorMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $params = $params ?? request()->all();

        if (empty($context)) {
            logger()->error('DataLoader->loadData: Site context is missing');

            return $this->getErrorMessage('Site context is missing', Response::HTTP_BAD_REQUEST);
        }

        if (empty($lang)) {
            logger()->error('DataLoader->loadData: Lang is missing');

            return $this->getErrorMessage('Lang is missing', Response::HTTP_BAD_REQUEST);
        }
        if (empty($platform)) {
            logger()->error('DataLoader->loadData: Platform is missing');

            return $this->getErrorMessage('Platform is missing', Response::HTTP_BAD_REQUEST);
        }
        if (empty($category)) {
            logger()->error('DataLoader->loadData: Category is missing');

            return $this->getErrorMessage('Category is missing', Response::HTTP_BAD_REQUEST);
        }

        //Getting previous session.
        $oldSiteId = htcms_get_siteId_for_admin();
        $oldLangId = htcms_get_language_id_for_admin();

        $microsite = (isset($microsite)) ? $microsite : 0; //will have something to handle later
        $microsite_id = $microsite;
        //$clearCache = $params['clearCache'] ?? false;

        $langData = Lang::where('iso_code', '=', $lang)->first();

        //Send error
        if ($langData === null) {
            logger()->error('DataLoader->loadData: Lang not found');

            return $this->getErrorMessage('Lang not found', Response::HTTP_BAD_REQUEST);
        }

        //set lang scope
        htcms_set_language_id_for_admin($langData->id); // this is required for Scope

        //Start fetching everything now
        $siteData = Site::with('lang')->where('context', $context)->first();

        //Send error
        if (empty($siteData)) {
            logger()->error('DataLoader->loadData: site not found');

            return $this->getErrorMessage('Site not found', Response::HTTP_BAD_REQUEST);
        }

        logger("DataLoader->loadData: Found site: {$siteData->id}, lang: {$langData->id}");

        //set site scope
        htcms_set_siteId_for_admin($siteData->id);

        //Set Context Vars: Site Id
        $this->infoLoader->setContextVars('site_id', $siteData->id);

        //platform
        $platformData = Platform::where('link_rewrite', $platform)->first();

        //parse url and choose link_rewrite
        $filterdCategory = $this->parseCategoryUrl($category);

        //category
        $categoryData = $filterdCategory['categoryData'];

        //Send error
        if ($categoryData === null) {
            logger('DataLoader->loadData: Category not found');

            return $this->getErrorMessage('Category not found', Response::HTTP_NOT_FOUND);
        }

        logger("DataLoader->loadData: Found category: {$categoryData->id}");

        //Set Context Vars: Category Id
        $this->infoLoader->setContextVars('category_id', $categoryData->id);

        if (! empty($categoryData->link_rewrite_pattern)) {
            $linkRewriteKey = str_replace(['{', '?', '}'], ['', '', ''], $categoryData->link_rewrite_pattern);
            $this->infoLoader->setContextVars($linkRewriteKey, $filterdCategory['param']);
        }

        /**
         * Fetch the theme from category_site
         */
        $categorySiteData = CategorySite::where([['platform_id', '=', $platformData->id], ['category_id', '=', $categoryData->id]])->first();

        //Send error
        if ($categorySiteData === null) {
            logger()->error('DataLoader->loadData: Category is not added in the site');

            return $this->getErrorMessage('Category not found', Response::HTTP_NOT_FOUND);
        }

        $categoryData->siteWise = $categorySiteData;

        logger("DataLoader->loadData: Found in category_site: {$categoryData->id} for platofrm: {$platformData->id}");

        $themeData = Theme::find($categorySiteData->theme_id);

        //Send error
        if ($themeData === null) {
            logger()->error('DataLoader->loadData: Theme is not defined');

            return $this->getErrorMessage('Theme is not defined', Response::HTTP_NOT_FOUND);
        }

        //props
        $propsData = SiteProp::where([['site_id', '=', $siteData->id],
            ['platform_id', '=', $platformData->id],
            ['is_public', '=', 1],
        ])->get();

        /**
         * Setting for global use
         * Set Context Vars:
         * int $category_id, int $site_id, int $platform_id, int $microsite_id=0
         */
        $this->infoLoader->setMultiContextVars($categoryData->id, $siteData->id, $platformData->id, $microsite_id);
        //Set Context Vars: Lang
        $this->infoLoader->setLanguageId($langData->id);
        /*** ========== end setting context ========= **/

        logger("loading data for: siteId: {$siteData->id}, micrositeId: {$microsite_id}, platformId: {$platformData->id}, langId: {$langData->id}, categoryId: {$categoryData->id}");

        //Get theme and hooks
        $parsedTheme = $this->parseThemeSkeleton($themeData->skeleton, $siteData->id, $microsite_id, $platformData->id, $langData->id, $categoryData->id);

        //Send error
        if (count($parsedTheme['hooks']) === 0 && count($parsedTheme['modules']) === 0) {
            logger()->error('DataLoader->loadData: There is not hook or module in the theme');

            return $this->getErrorMessage('There is not hook or module in the theme', Response::HTTP_NOT_FOUND);
        }

        //Add hooks and modules
        $themeData->hooks = (HookResource::collection($parsedTheme['hooks']))->toArray(request());
        $themeData->modules = (ModuleResource::collection($parsedTheme['modules']))->toArray(request());

        //Convert for api
        $langInfo = (new LangResource($langData))->toArray(request());
        $siteInfo = (new SiteResource($siteData))->toArray(request());
        $platformInfo = (new PlatformResource($platformData))->toArray(request());
        $categoryInfo = (new CategoryResource($categoryData))->toArray(request());

        //$categorySiteInfo = (new CategorySiteResource($categorySiteData))->toArray(request());
        $themeInfo = (new ThemeResource($themeData))->toArray(request());
        $propsInfo = (SitePropResource::collection($propsData))->toArray(request());

        //Get html meta
        $htmlMetaData = $this->getHtmlMetaData($siteData, $themeData, $categoryData);

        $data['isLoginRequired'] = $isLoginRequired = $categoryData->required_login === 1 || $this->moduleLoader->isLoginRequired();
        $data['isContentFound'] = $this->moduleLoader->isContentFound();
        $data['totalModules'] = $this->moduleLoader->getModulesCount();

        $data['html'] = $htmlMetaData['html'];
        $data['meta'] = [
            'site' => $siteInfo,
            'platform' => $platformInfo,
            'lang' => $langInfo,
            'category' => $categoryInfo,
            'page' => $htmlMetaData['page'],
            'theme' => $themeInfo,
            'props' => $propsInfo,
        ];

        //Setting it back otherwise it will affect admin panel too.
        htcms_set_siteId_for_admin($oldSiteId);
        htcms_set_language_id_for_admin($oldLangId);

        logger("loading data completed for: $category ({$categoryData->id}), context: $context ({$siteData->id}), platform: $platform ({$platformData->id}) lang: $lang ({$langData->id})");

        return $data;

    }

    /**
     * load from external api
     *
     * @return void
     */
    public function loadDataFromExternalApi(string $context, ?string $lang = null, ?string $platform = null, ?string $category = null, ?string $microsite = null)
    {
        try {
            $apiUrl = app()->HashtagCms->getLoadDataApiSource();

            $apiSecretAndContext = $this->getApiKeyAndContext($apiUrl);

            if ($apiSecretAndContext['apiSecret'] == null) {
                throw new \Exception('Unable to find api secret key in config', Response::HTTP_BAD_REQUEST);
            }

            $apiSecret = $apiSecretAndContext['apiSecret'];
            $context = $apiSecretAndContext['context'];

            $fullUrl = request()->fullUrl();
            $queryParams = '';

            if (strpos($fullUrl, '?') > 0) {
                $queryParams = substr($fullUrl, strpos($fullUrl, '?') + 1, strlen($fullUrl));
            }

            $apiUrl .= "?site={$context}&platform={$platform}&lang={$lang}&category={$category}&api_secret={$apiSecret}";

            if ($queryParams != '') {
                $apiUrl .= "&{$queryParams}";
            }

            $headers['Content-Type'] = 'application/json';
            $headers['api_key'] = $apiSecret;
            $http = Http::withHeaders($headers)->get($apiUrl);

            if ($http->status() == 200) {
                $data = $http->json();

                //Old Compatibility
                if (! isset($data['meta']['page'])) {
                    $data['meta']['page'] = ['id' => -1, 'linkRewrite' => '', 'activeKey' => '', 'name' => ''];
                }
                if (! isset($data['isLoginRequired'])) {
                    $data['isLoginRequired'] = false;
                }
                if (! isset($data['isContentFound'])) {
                    $data['isContentFound'] = true;
                }

            } else {
                $msg = $http->reason()." :from API: $apiUrl";
                logger()->error($msg);
                throw new \Exception($msg, $http->status());
            }
        } catch (\Exception $exception) {
            $data = ['status' => Response::HTTP_PRECONDITION_FAILED, 'message' => $exception->getMessage()];
        }

        return $data;
    }

    /**
     * Parse skeleton
     *
     * @param  int  $site_id
     * @return array[]
     */
    private function parseThemeSkeleton(string $skeleton, int $siteId, int $micrositeId, int $platformId, int $langId, int $categoryId): array
    {

        $this->infoLoader->setMultiContextVars($categoryId, $siteId, $platformId, $micrositeId);
        $this->infoLoader->setLanguageId($langId);

        $subject = $skeleton;
        $pattern = "/\%.*?\%/";
        preg_match_all($pattern, $subject, $matches); //PREG_OFFSET_CAPTURE
        $allHooks = [];
        $allModules = [];

        //Parse all hooks and modules in a theme skeleton

        if (count($matches) > 0) {
            $matches = $matches[0];
            foreach ($matches as $key => $val) {
                $current = $val;
                $isHook = str_contains($current, '%{cms.hook.');
                $isModule = str_contains($current, '%{cms.module.');

                if ($isHook) {
                    $patterns = [];
                    $patterns[0] = '/%/';
                    $patterns[1] = '/{cms.hook./';
                    $patterns[2] = '/}/';
                    $replacements = [];
                    $replacements[2] = '';
                    $replacements[1] = '';
                    $replacements[0] = '';
                    $name = preg_replace($patterns, $replacements, $current);
                    $hookData = Hook::where('alias', '=', $name)->with('site', function ($q) use ($siteId) {
                        $q->where('site_id', '=', $siteId);
                    })->first();
                    $hookData->modules = [];
                    if ($hookData != null && count($hookData->site) > 0) {
                        unset($hookData->site);
                        //get modules by hooks

                        $hookData->modules = $this->getModulesByHook($hookData->id, $siteId, $micrositeId, $platformId, $langId, $categoryId);
                        $allHooks[] = $hookData;
                    }
                }

                //Check if there is any module in theme
                if ($isModule) {
                    $patterns = [];
                    $patterns[0] = '/%/';
                    $patterns[1] = '/{cms.module./';
                    $patterns[2] = '/}/';
                    $replacements = [];
                    $replacements[2] = '';
                    $replacements[1] = '';
                    $replacements[0] = '';
                    $moduleName = preg_replace($patterns, $replacements, $current);
                    $moduleData = Module::where([['alias', '=', rtrim(ltrim($moduleName))], ['site_id', '=', $siteId]])->first();

                    if ($moduleData != null) {
                        $moduleData->data = $this->moduleLoader->getModuleData($moduleData);
                        $moduleData->moduleProps = $this->moduleLoader->getModuleProps($moduleData->id, $siteId, $platformId);
                        $allModules[] = $moduleData; ///new ModuleResource(); is not required here. converting it to collection in loadData;
                    }
                }
            }
        }

        return ['hooks' => $allHooks, 'modules' => $allModules];
    }

    /**
     * Get modules by hook id
     */
    private function getModulesByHook(int $hookId, int $siteId, int $micrositeId, int $platformId, int $langId, int $categoryId): array
    {
        $this->infoLoader->setMultiContextVars($categoryId, $siteId, $platformId, $micrositeId);
        $this->infoLoader->setLanguageId($langId);
        //fetch site modules by site, (?microsite @todo: will handle later), platform, hook, category order by position
        $moduleWhere = [
            ['hook_id', '=', $hookId],
            ['site_id', '=', $siteId],
            ['microsite_id', '=', $micrositeId],
            ['platform_id', '=', $platformId],
            ['category_id', '=', $categoryId],
        ];

        $moduleWhere = [['hook_id', '=', $hookId], ['site_id', '=', $siteId], ['microsite_id', '=', $micrositeId], ['platform_id', '=', $platformId], ['category_id', '=', $categoryId]];

        $allModules = ModuleSite::where($moduleWhere)->orderBy('position', 'ASC')->get();
        $modulesArr = [];
        foreach ($allModules as $module) {
            $moduleData = Module::find($module->module_id);
            $moduleResultData = $this->moduleLoader->getModuleData($moduleData);
            if (isset($moduleResultData['queryData'])) {
                $moduleData->data = $moduleResultData['serviceData'];
                $moduleData->queryData = $moduleResultData['queryData'];
            } else {
                $moduleData->data = $moduleResultData;
            }
            $moduleData->moduleProps = $this->moduleLoader->getModuleProps($moduleData->id, $siteId, $platformId);
            $modulesArr[] = (new ModuleResource($moduleData))->toArray(request());
        }

        return $modulesArr;
    }

    /**
     * Get html meta data
     */
    private function getHtmlMetaData(Site $siteData, Theme $themeData, Category $categoryData): array
    {

        $theme_dir = $themeData->directory;

        $neg = base64_decode('PG1ldGEgbmFtZT0iZ2VuZXJhdG9yIiBuYW1lPSIjQ01TIChodHRwczovL3d3dy5oYXNodGFnY21zLm9yZy8pIj4=');

        $categoryHeaderContent = $this->parseStringForPath($categoryData->siteWise->header_content, $theme_dir);
        $categoryFooterContent = $this->parseStringForPath($categoryData->siteWise->footer_content, $theme_dir);

        //theme header/footer
        $themeInfo['header_content'] = $neg.$this->parseStringForPath($themeData->header_content, $theme_dir);
        $themeInfo['footer_content'] = $this->parseStringForPath($themeData->footer_content, $theme_dir);
        $themeInfo['skeleton'] = $this->parseStringForPath($themeData->skeleton, $theme_dir);

        //if any module is as a seo module
        $seoContent = $this->moduleLoader->getSeoContent();

        $metaDesc = $categoryMetaDesc = $categoryData->lang->meta_description;
        $metaKeywords = $categoryKeywords = $categoryData->lang->meta_keywords;
        $metaRobots = $categoryMetaRobots = ($categoryData->lang->meta_robots == null) ? 'index, follow' : $categoryData->lang->meta_robots;
        $metaCanonical = $categoryMetaCanonical = $categoryData->lang->meta_canonical;
        //Category meta title or category title
        $categoryTitle = (empty($categoryData->lang->meta_title)) ? $categoryData->lang->title : $categoryData->lang->meta_title;

        // SEO handling
        // if any module is seo module
        // fetch data from that
        // else category meta info
        // else site meta info
        $pageInfo = ['id' => -1, 'linkRewrite' => '', 'activeKey' => '', 'name' => ''];

        if ($seoContent != null) {
            $metaDesc = ($seoContent['metaDescription'] == null) ? $categoryMetaDesc : $seoContent['metaDescription'];
            $metaKeywords = ($seoContent['metaKeywords'] == null) ? $categoryKeywords : $seoContent['metaKeywords'];
            $metaRobots = ($seoContent['metaRobots'] == null) ? $categoryMetaRobots : $seoContent['metaRobots'];
            $metaCanonical = ($seoContent['metaCanonical'] == null) ? $categoryMetaCanonical : $seoContent['metaCanonical'];
            //Change category title if meta has title
            $categoryTitle = ($seoContent['metaTitle'] == null) ? $categoryTitle : $seoContent['metaTitle'];

            //add seo module header/footer content

            $categoryHeaderContent = $categoryHeaderContent.$this->parseStringForPath($seoContent['headerContent'] ?? '', $theme_dir);
            $categoryFooterContent = $categoryFooterContent.$this->parseStringForPath($seoContent['footerContent'] ?? '', $theme_dir);

            //save it for later; might deprecate
            $pageInfo['id'] = ($seoContent['page_id'] == null) ? '' : $seoContent['page_id'];
            $pageInfo['linkRewrite'] = ($seoContent['link_rewrite'] == null) ? '' : $seoContent['link_rewrite'];
            $pageInfo['activeKey'] = ($seoContent['activeKey'] == null) ? '' : $seoContent['activeKey'];
            $pageInfo['name'] = ($seoContent['page_name'] == null) ? '' : $seoContent['page_name'];
        }

        $metaTitle = (empty($categoryTitle)) ? $siteData->lang->title : $categoryTitle;

        $headerMeta = [
            'metaCanonical' => $metaCanonical,
            'metaDescription' => $metaDesc,
            'metaKeywords' => $metaKeywords,
            'metaRobots' => $metaRobots,
            'metaTitle' => $metaTitle,
        ];

        $metaLinks = [];

        /*if($metaCanonical !== null) {
            $metaLinks[] = array("rel" => "canonical", "href" => $metaCanonical);
        }*/
        //fav icon
        if (isset($siteData->favicon) && ! empty(trim($siteData->favicon))) {
            $metaLinks[] = ['rel' => 'shortcut icon', 'href' => htcms_get_media($siteData->favicon)]; //this helper is in admin
        } else {
            //add default icon
            $metaLinks[] = ['rel' => 'shortcut icon', 'href' => $this->parseStringForPath('%{image_path}%/favicon.png', $theme_dir)];
        }
        $metaContent = '';
        if (count($metaLinks) > 0) {
            foreach ($metaLinks as $link) {
                $metaContent .= "<link rel='$link[rel]' href='$link[href]' />";
            }
        }
        foreach ($headerMeta as $mKey => $hMeta) {
            $metaContent .= '<meta name="'.$mKey.'" content="'.$hMeta.'" />';
        }
        //Making header data

        $headTag = [];
        $headTag['headerContent'] = [
            ['order' => 1, 'html' => $themeInfo['header_content'].$categoryHeaderContent],
        ];
        $headTag['title'] = $metaTitle;
        $headTag['meta'] = $headerMeta;
        $headTag['links'] = $metaLinks;

        $bodyTag = [];
        $bodyTag['content'] = ['skeleton' => $themeInfo['skeleton']];
        $bodyTag['footer']['footerContent'][] = ['order' => 1, 'html' => $themeInfo['footer_content'].$categoryFooterContent];

        //Set html
        $data['html']['head'] = $headTag;
        $data['html']['body'] = $bodyTag;

        $data['page'] = $pageInfo;

        return $data;
    }

    /**
     * Get category and link_rewrite
     *
     * @return void
     */
    private function parseCategoryUrl(string $path): array
    {
        // if path is "/" -> search for / category or get the default site category link_rewrite

        $pathArr = explode('/', $path);
        $linkRewrite = $pathArr[0];
        $param = '';
        $selectedCategory = null;
        if (count($pathArr) > 1) {
            array_shift($pathArr); //remove first one
            $param = implode('/', $pathArr);
        }

        $categoryData = Category::with(['lang'])->where([['link_rewrite', '=', $linkRewrite], ['publish_status', '=', 1]])
            ->orWhere([['link_rewrite', '=', $path], ['publish_status', '=', 1]])->get();
        if ($categoryData->count() > 0) {
            foreach ($categoryData as $category) {
                if ($category->link_rewrite === $path) {
                    $selectedCategory = $category;
                    break;
                }
            }
            $selectedCategory = ($selectedCategory !== null) ? $selectedCategory : $categoryData[0];
        }
        $isParamRequired = Str::contains($selectedCategory->link_rewrite_pattern ?? '', '?') ? false : true;

        return ['linkRewrite' => $linkRewrite, 'param' => $param, 'fullPath' => $path, 'categoryData' => $selectedCategory, 'paramRequired' => $isParamRequired];
    }

    /**
     * Get api key
     *
     * @return mixed|null
     */
    private function getApiKeyAndContext(string $url)
    {
        $domain = parse_url($url)['host'];
        $domainList = config('hashtagcms.domains');
        $context = $domainList[$domain];

        $apiSecretList = config('hashtagcms.api_secrets');
        $apiSecret = $apiSecretList[$context] ?? null;
        $data['apiSecret'] = $apiSecret;
        $data['context'] = $context;

        return $data;
    }

    /**
     * Get error message
     */
    protected function getErrorMessage(string $message, int $status, array $withData = []): array
    {
        $error = ['message' => $message, 'status' => $status];

        return array_merge($error, $withData);
    }
}
