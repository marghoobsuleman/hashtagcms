<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;

use Illuminate\Support\Arr;
use App\Http\Resources\ModuleResource;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\DB;
use MarghoobSuleman\HashtagCms\Models\Module;
use MarghoobSuleman\HashtagCms\Models\ModuleProp;
use MarghoobSuleman\HashtagCms\Models\ModuleSite;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\Response;

use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\CategorySite;
use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\Platform;
use MarghoobSuleman\HashtagCms\Models\SiteProp;
use MarghoobSuleman\HashtagCms\Models\Theme;
use MarghoobSuleman\HashtagCms\Models\Hook;

/** v2 */
use App\Http\Resources\SiteResource;
use App\Http\Resources\SiteCollection;
use App\Http\Resources\PlatformResource;
use App\Http\Resources\LangResource;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\ZoneResource;
use App\Http\Resources\SitePropResource;
use App\Http\Resources\CategorySiteResource;
use App\Http\Resources\ThemeResource;
use App\Http\Resources\HookResource;
use App\Http\Resources\ModulePropResource;


use MarghoobSuleman\HashtagCms\Core\Traits\LayoutHandler;

class DataLoaderV2
{
    use LayoutHandler;

    protected InfoLoader $infoLoader;
    protected CacheManager $cacheManager;
    protected LayoutManager $layoutManager;
    protected ModuleLoader $moduleLoader;

    function __construct()
    {
        $this->infoLoader = app()->HashtagCms->infoLoader();
        $this->layoutManager = app()->HashtagCms->layoutManager();
        $this->cacheManager = app()->HashtagCms->cacheManager();
        $this->moduleLoader = app()->HashtagCms->moduleLoader();
    }

    /**
     * Load config
     * @param string $context
     * @param string|null $lang
     * @param string|null $platform
     * @return array
     */

    public function loadConfig(string $context, string $lang=null, string $platform=null) {

       try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
           info("DataLoader->loadConfig: Database Error: ".$e->getMessage());
            return $this->getErrorMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        // fetch site info from request -> context
        // fetch lang info from request -> lang
        // fetch platform info from request -> platform
        // if lang is passed in request use that else fetch lang_id from site data
        // if platform is passed in request use that else fetch platform_id from site data
        // after that fetch all the info.

        if (empty($context)) {
            info("ServiceLoader>allConfigs: Site context is missing");
            return $this->getErrorMessage("Site context is missing", Response::HTTP_BAD_REQUEST);
        }

        $oldSiteId = htcms_get_siteId_for_admin();
        $oldLangId = htcms_get_language_id_for_admin();

        $lang_id = null;
        $platform_id = null;
        $site = Site::where('context', '=', $context)->first();

        if (empty($site)) {
            info("ServiceLoader>allConfigs: Site not found");
            return $this->getErrorMessage("Site not found", Response::HTTP_NOT_FOUND);
        }

        //if lang is not empty fetch the lang info.
        // If found use that else use default lang from the site
        if(!empty($lang)) {
            $langInfo = Lang::where('iso_code', '=', $lang)->first();
            if(!empty($langInfo)) {
                $lang_id = $langInfo->id;
            }
        }

        //if platform is not empty fetch form the
        if(!empty($platform)) {
            $platformInfo = Platform::where('link_rewrite', '=', $platform)->first();
            if(!empty($platformInfo)) {
                $platform_id = $platformInfo->id;
            }
        }
        //set default
        $site_id = $site->id;
        $lang_id = ($lang_id == null) ? $site->lang_id : $lang_id;
        $platform_id = ($platform_id == null) ? $site->platform_id : $platform_id; //will use this

        /** set scope lang id */
        htcms_set_language_id_for_admin($lang_id);


        //Start fetching everything now
        $siteData = Site::with('lang')->where('context', $context)->first();
        $propsData = SiteProp::where(array(array('site_id', '=', $site_id), array('platform_id', '=', $platform_id)))->get();

        //defaults
        $defaultData['categoryId'] = $siteData->category_id;
        $defaultData['themeId'] = $siteData->theme_id;
        $defaultData['platformId'] = $siteData->platform_id;
        $defaultData['langId'] = $siteData->lang_id;
        $defaultData['countryId'] = $siteData->country_id;
        $defaultData['currencyId'] = $siteData->currency_id;

        //convert to resource
        $siteInfo = new SiteResource($siteData);
        $platformsInfo = PlatformResource::collection($siteData->platform);
        $langsInfo = LangResource::collection($siteData->language);
        $currenciesInfo = CurrencyResource::collection($siteData->currency);
        $zonesInfo = ZoneResource::collection($siteData->zone);
        $countriesInfo = CountryResource::collection($siteData->country);
        $categoriesInfo = CategoryResource::collection($siteData->categoryLang);
        $propsInfo = SitePropResource::collection($propsData);

        $data['site'] = $siteInfo;

        $data['defaultData'] = $defaultData;
        $data['platforms'] = $platformsInfo;
        $data['langs'] = $langsInfo;
        $data['currencies'] = $currenciesInfo;
        $data['zones'] = $zonesInfo;
        $data['countries'] = $countriesInfo;
        $data['categories'] = $categoriesInfo;
        $data['props'] = $propsInfo;

        /**
         * reset scopes
         */
        htcms_set_site_id_for_admin($oldSiteId);
        htcms_set_language_id_for_admin($oldLangId);

        return $data;
    }


    /**
     * Load data
     * @param string $context
     * @param string|null $lang
     * @param string|null $platform
     * @param string|null $category
     * @param string|null $microsite
     * @return mixed
     */
    public function loadData(string $context, string $lang=null, string $platform=null, string $category=null, string $microsite=null): mixed
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            info("DataLoader->loadData: Database Error: ".$e->getMessage());
            return $this->getErrorMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $params = $params ?? request()->all();

        if(empty($context)) {
            info("DataLoader->loadData: Site context is missing");
            return $this->getErrorMessage("Site context is missing", Response::HTTP_BAD_REQUEST);
        }

        if(empty($lang)) {
            info("DataLoader->loadData: Lang is missing");
            return $this->getErrorMessage("Lang is missing", Response::HTTP_BAD_REQUEST);
        }
        if(empty($platform)) {
            info("DataLoader->loadData: Platform is missing");
            return $this->getErrorMessage("Platform is missing", Response::HTTP_BAD_REQUEST);
        }
        if(empty($category)) {
            info("DataLoader->loadData: Category is missing");
            return $this->getErrorMessage("Category is missing", Response::HTTP_BAD_REQUEST);
        }

        //Getting previous session.
        $oldSiteId = htcms_get_siteId_for_admin();
        $oldLangId = htcms_get_language_id_for_admin();

        $microsite = (isset($microsite)) ? $microsite :  0; //will have something to handle later
        $microsite_id = $microsite;
        //$clearCache = $params['clearCache'] ?? false;

        $langData = Lang::where('iso_code', '=', $lang)->first();

        //set lang scope
        htcms_set_language_id_for_admin($langData->id); // this is required for Scope

        //Start fetching everything now
        $siteData = Site::with('lang')->where('context', $context)->first();

        //set site scope
        htcms_set_site_id_for_admin($siteData->id);

        //Set Context Vars: Site Id
        $this->infoLoader->setContextVars('site_id', $siteData->id);

        //lang
        $langInfo = new LangResource($langData);

        //platform
        $platformData = Platform::where("link_rewrite", $platform)->first();

        //parse url and choose link_rewrite
        $filterdCategory = $this->parseCategoryUrl($category);

        //category
        $categoryData = $filterdCategory['categoryData'];

        if($categoryData === null) {
            info("DataLoader->loadData: Category not found");
            return $this->getErrorMessage("Category not found", Response::HTTP_NOT_FOUND);
        }

        //Set Context Vars: Category Id
        $this->infoLoader->setContextVars('category_id', $categoryData->id);

        if (!empty($categoryData->link_rewrite_pattern)) {
            $linkRewriteKey = str_replace(array("{", "?", "}"), array("", "", ""), $categoryData->link_rewrite_pattern);
            $this->infoLoader->setContextVars($linkRewriteKey, $filterdCategory['param']);
        }

        /**
         * Fetch the theme from category_site
         */
        $categorySiteData = CategorySite::where(array(array('platform_id', '=', $platformData->id), array('category_id', '=', $categoryData->id)))->first();
        $categoryData->site = $categorySiteData;

        $themeData = Theme::find($categorySiteData->theme_id);

        //props
        $propsData = SiteProp::where(array(array('site_id', '=', $siteData->id), array('platform_id', '=', $platformData->id)))->get();

        /**
         * Set Context Vars:
         * int $category_id, int $site_id, int $platform_id, int $microsite_id=0
         */
        $this->infoLoader->setMultiContextVars($categoryData->id, $siteData->id, $platformData->id, $microsite_id);
        //Set Context Vars: Lang
        $this->infoLoader->setLanguageId($langData->id);
        /*** ========== end setting context ========= **/


        //Get theme and hooks
        $parsedTheme = $this->parseThemeSkeleton($themeData->skeleton, $siteData->id, $microsite_id, $platformData->id, $langData->id, $categoryData->id);

        //Add hooks and modules
        $themeData->hooks = HookResource::collection($parsedTheme['hooks']);
        $themeData->modules = ModuleResource::collection($parsedTheme['modules']);

        //Convert for api
        $siteInfo = new SiteResource($siteData);
        $platformInfo = new PlatformResource($platformData);
        $categoryInfo = new CategoryResource($categoryData);
        $categorySiteInfo = new CategorySiteResource($categorySiteData);
        $themeInfo = new ThemeResource($themeData);
        $propsInfo = SitePropResource::collection($propsData);



        $htmlMetaData = $this->getHtmlMetaData($siteData, $themeData, $categoryData);

        $data['html'] = $htmlMetaData['html'];

        $data['meta'] = array("site"=>$siteInfo,
            "platform"=>$platformInfo,
            "lang"=>$langInfo,
            "category"=>$categoryInfo,
            "theme"=>$themeInfo,
            "props"=>$propsInfo
            );

        //$data['isLoginRequired'] = $isLoginRequired;
        //$data['status'] = Response::HTTP_OK;

        //Setting it back otherwise it will affect admin panel too.
        htcms_set_site_id_for_admin($oldSiteId);
        htcms_set_language_id_for_admin($oldLangId);

        //info("loadData: Fetching completed for category: $category, context: $context, platform: $platform lang: $lang");
        return $data;

    }


    /**
     * Parse skeleton
     * @param string $skeleton
     * @param int $site_id
     * @return array[]
     */
    private function parseThemeSkeleton(string $skeleton, int $siteId, int $micrositeId=0, int $platformId, int $langId, int $categoryId) {

            $this->infoLoader->setMultiContextVars($categoryId, $siteId, $platformId, $micrositeId);
            $this->infoLoader->setLanguageId($langId);

            $subject = $skeleton;
            $pattern = "/\%.*?\%/";
            preg_match_all($pattern, $subject, $matches); //PREG_OFFSET_CAPTURE
            $allHooks = array();
            $allModules = array();

            //Parse all hooks and modules in a theme skeleton

            if(count($matches)>0) {
                $matches = $matches[0];
                foreach($matches as $key=>$val) {
                    $current = $val;
                    $isHook = str_contains($current, "%{cms.hook.");
                    $isModule = str_contains($current, "%{cms.module.");

                    if($isHook) {
                        $patterns = array();
                        $patterns[0] = '/%/';
                        $patterns[1] = '/{cms.hook./';
                        $patterns[2] = '/}/';
                        $replacements = array();
                        $replacements[2] = '';
                        $replacements[1] = '';
                        $replacements[0] = '';
                        $name = preg_replace($patterns, $replacements, $current);
                        $hookData = Hook::where('alias', '=', $name)->with('site', function($q) use($siteId) {$q->where('site_id', '=', $siteId);})->first();
                        $hookData->modules = [];
                        if ($hookData != null && sizeof($hookData->site) > 0) {
                            unset($hookData->site);
                            //get modules by hooks
                            $hookData->modules = $this->getModulesByHook($hookData->id, $siteId, $micrositeId, $platformId, $langId, $categoryId);
                            $allHooks[] = $hookData;
                        }
                    }

                    //Check if there is any module in theme
                    if($isModule) {
                        $patterns = array();
                        $patterns[0] = '/%/';
                        $patterns[1] = '/{cms.module./';
                        $patterns[2] = '/}/';
                        $replacements = array();
                        $replacements[2] = '';
                        $replacements[1] = '';
                        $replacements[0] = '';
                        $moduleName = preg_replace($patterns, $replacements, $current);
                        $moduleData = Module::where(array(array('alias', '=', rtrim(ltrim($moduleName))), array('site_id', '=', $siteId)))->first();

                        if($moduleData!=null) {
                            $moduleData->data = $this->moduleLoader->getModuleData($moduleData);
                            $moduleData->moduleProps = $this->moduleLoader->getModuleProps($moduleData->id, $siteId, $platformId);
                            $allModules[] = $moduleData; ///new ModuleResource(); is not required here. converting it to collection in loadData;
                        }
                    }
                }
            }

            return array("hooks"=>$allHooks, "modules"=>$allModules);
    }

    /**
     * Get modules by hook id
     * @param int $hookId
     * @param int $siteId
     * @param int $micrositeId
     * @param int $platformId
     * @param int $langId
     * @param int $categoryId
     * @return array
     */
    private function getModulesByHook(int $hookId, int $siteId, int $micrositeId=0, int $platformId, int $langId, int $categoryId) {
        $this->infoLoader->setMultiContextVars($categoryId, $siteId, $platformId, $micrositeId);
        $this->infoLoader->setLanguageId($langId);
        //fetch site modules by site, (?microsite @todo: will handle later), platform, hook, category order by position
        $moduleWhere = array(
            array('hook_id', '=', $hookId),
            array('site_id', '=', $siteId),
            array('microsite_id', '=', $micrositeId),
            array('platform_id', '=', $platformId),
            array('category_id', '=', $categoryId)
        );
        $allModules = ModuleSite::where($moduleWhere)->get();
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
            $modulesArr[] = new ModuleResource($moduleData);
        }
        return $modulesArr;
    }


    /**
     * Get html meta data
     * @param Theme $themeData
     * @param Category $categoryData
     * @return array
     */
    private function getHtmlMetaData(Site $siteData, Theme $themeData, Category $categoryData):array {

        $theme_dir = $themeData->directory;

        $neg = base64_decode('PG1ldGEgbmFtZT0iZ2VuZXJhdG9yIiBuYW1lPSIjQ01TIChodHRwczovL3d3dy5oYXNodGFnY21zLm9yZy8pIj4=');

        $categoryHeaderContent = $this->parseStringForPath($categoryData->site->header_content, $theme_dir);
        $categoryFooterContent = $this->parseStringForPath($categoryData->site->footer_content, $theme_dir);

        //theme header/footer
        $themeInfo["header_content"] = $neg.$this->parseStringForPath($themeData->header_content, $theme_dir);
        $themeInfo["footer_content"] = $this->parseStringForPath($themeData->footer_content, $theme_dir);
        $themeInfo["skeleton"] = $this->parseStringForPath($themeData->skeleton, $theme_dir);


        //if any module is as a seo module
        $seoContent = $this->moduleLoader->getSeoContent();

        $metaDesc = $categoryMetaDesc = $categoryData->lang->meta_description;
        $metaKeywords = $categoryKeywords = $categoryData->lang->meta_keywords;
        $metaRobots = $categoryMetaRobots = ($categoryData->lang->meta_robots == null) ? "index, follow" : $categoryData->lang->meta_robots;
        $metaCanonical = $categoryMetaCanonical = $categoryData->lang->meta_canonical;
        //Category meta title or category title
        $categoryTitle = (empty($categoryData->lang->meta_title)) ? $categoryData->lang->title : $categoryData->lang->meta_title;

        // SEO handling
        // if any module is seo module
        // fetch data from that
        // else category meta info
        // else site meta info

        if($seoContent != null) {
            $metaDesc = ($seoContent["metaDescription"] == null) ? $categoryMetaDesc : $seoContent["metaDescription"];
            $metaKeywords = ($seoContent["metaKeywords"] == null) ? $categoryKeywords : $seoContent["metaKeywords"];
            $metaRobots = ($seoContent["metaRobots"] == null) ? $categoryMetaRobots : $seoContent["metaRobots"];
            $metaCanonical = ($seoContent["metaCanonical"] == null) ? $categoryMetaCanonical : $seoContent["metaCanonical"];
            //Change category title if meta has title
            $categoryTitle = ($seoContent["metaTitle"] == null) ? $categoryTitle : $seoContent["metaTitle"];

            //add seo module header/footer content

            $categoryHeaderContent = $categoryHeaderContent.$this->parseStringForPath($seoContent["headerContent"] ?? "", $theme_dir);
            $categoryFooterContent = $categoryFooterContent.$this->parseStringForPath($seoContent["footerContent"] ?? "", $theme_dir);

            //save it for later; might deprecate
            $categorySiteInfo["page_active_key"] =  ($seoContent["activeKey"] == null) ? "" : $seoContent["activeKey"];
            $categorySiteInfo["page_link_rewrite"] =  ($seoContent["link_rewrite"] == null) ? "" : $seoContent["link_rewrite"];
            $categorySiteInfo["page_id"] =  ($seoContent["page_id"] == null) ? "" : $seoContent["page_id"];
            $categorySiteInfo["page_name"] =  ($seoContent["page_name"] == null) ? "" : $seoContent["page_name"];
        }

        $metaTitle = (empty($categoryTitle)) ? $siteData->lang->title : $categoryTitle;

        $headerMeta = array(
            "metaCanonical"=>$metaCanonical,
            "metaDescription"=>$metaDesc,
            "metaKeywords"=>$metaKeywords,
            "metaRobots"=>$metaRobots,
            "metaTitle"=>$metaTitle
        );


        $metaLinks = array();

        /*if($metaCanonical !== null) {
            $metaLinks[] = array("rel" => "canonical", "href" => $metaCanonical);
        }*/
        //fav icon
        if(isset($siteData->favicon) && !empty(trim($siteData->favicon))) {
            $metaLinks[] = array("rel"=>"shortcut icon", "href"=>htcms_get_media($siteData->favicon)); //this helper is in admin
        } else {
            //add default icon
            $metaLinks[] = array("rel"=>"shortcut icon", "href"=>$this->parseStringForPath("%{image_path}%/favicon.png", $theme_dir));
        }
        $metaContent = "";
        if(sizeof($metaLinks)>0) {
            foreach ($metaLinks as $link) {
                $metaContent .= "<link rel='$link[rel]' href='$link[href]' />";
            }
        }
        foreach ($headerMeta as $mKey=>$hMeta) {
            $metaContent .= '<meta name="'.$mKey.'" content="'.$hMeta.'" />';
        }
        //Making header data

        $headTag = array();
        $headTag['headerContent'] = array(
            array("order"=>1, "html"=>$themeInfo["header_content"].$categoryHeaderContent)
        );
        $headTag['title'] = $metaTitle;
        $headTag['meta'] = $headerMeta;
        $headTag['links'] = $metaLinks;

        $bodyTag = array();
        $bodyTag["content"] = array("skeleton"=> $themeInfo["skeleton"]);
        $bodyTag["footer"] = array("footerContent"=> array("order"=>1, "html"=>$themeInfo["footer_content"].$categoryFooterContent));

        //Set html
        $data['html']["head"] = $headTag;
        $data['html']["body"] = $bodyTag;

        return $data;
    }

    /**
     * Get category and link_rewrite
     * @param string $path
     * @return void
     */
    private function parseCategoryUrl(string $path):array {
        // if path is "/" -> search for / category or get the default site category link_rewrite

        $pathArr = explode("/", $path);
        $linkRewrite = $pathArr[0];
        $param = "";
        $selectedCategory = null;
        if (sizeof($pathArr) > 1) {
            array_shift($pathArr); //remove first one
            $param = join("/", $pathArr);
        }

        $categoryData = Category::with(['lang'])->where(array(array('link_rewrite', '=', $linkRewrite), array('publish_status', '=', 1)))
                                                ->orWhere(array(array('link_rewrite', '=', $path), array('publish_status', '=', 1)))->get();
        if($categoryData->count() > 0) {
            foreach ($categoryData as $category) {
                if ($category->link_rewrite === $path) {
                    $selectedCategory = $category;
                    break;
                }
            }
            $selectedCategory = ($selectedCategory !== null) ? $selectedCategory : $categoryData[0];
        }

        return array("linkRewrite"=>$linkRewrite, "param"=>$param, "fullPath"=>$path, "categoryData"=>$selectedCategory);
    }


    /**
     * Get error message
     * @param string $message
     * @param int $status
     * @param array $withData
     * @return array
     */
    protected function getErrorMessage(string $message, int $status, array $withData=array()):array
    {
        $error = array("message"=>$message, "status"=>$status);
        return array_merge($error, $withData);
    }
}
