<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;
use JetBrains\PhpStorm\ArrayShape;
use MarghoobSuleman\HashtagCms\Core\Common;
//use MarghoobSuleman\HashtagCms\Core\Main\ModuleLoader;

class DataLoader
{
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
     * Load data
     * @param array|null $params
     * @return mixed
     */
    public function loadData(array $params=null): mixed
    {

        $params = $params ?? request()->all();

        if(empty($params['site'])) {
            return $this->getErrorMessage("Site context is missing", 400);
        }

        if(empty($params['lang'])) {
            return $this->getErrorMessage("Lang is missing", 400);
        }
        if(empty($params['tenant'])) {
            return $this->getErrorMessage("Tenant is missing", 400);
        }
        if(empty($params['category'])) {
            return $this->getErrorMessage("Category is missing", 400);
        }

        $context = $params['site'];
        $lang = $params['lang'];
        $tenant = $params['tenant'];
        $category = $params['category'];
        $microsite = $params['microsite'] ?? 0; //will have something to handle later
        $microsite_id = $microsite;
        $contentUrl = $params['contentUrl'] ?? "";
        $clearCache = $params['clearCache'] ?? false;

        //Need language first
        //check lang info
        $langIsoCacheKey = $lang;

        if(!$this->cacheManager->exists($langIsoCacheKey) || $clearCache) {
            info("loadData: Fetching lang info iso code: $lang");
            //noinspection ConstantConditions
            $langInfo = $this->infoLoader->getLangInfo($lang, null);

            //Stop everything if site info is not correct.
            if(empty($langInfo)) {
                info("loadData: Language not found!");
                return $this->getErrorMessage("Language not found", 400);
            }
            $this->cacheManager->put($langIsoCacheKey, $langInfo);

        } else {
            info("loadData: From Cache ($langIsoCacheKey): Fetching site info, context: $context");
            $langInfo = $this->cacheManager->get($langIsoCacheKey);
        }
        $langInfo = $langInfo->toArray();

        app()->HashtagCms->infoLoader()->setInfoKeeper("lang_id", $langInfo['id']);

        //check site - return 404 if not found
        $contextCacheKey = $context;
        if(!$this->cacheManager->exists($contextCacheKey) || $clearCache) {
            info("loadData: Fetching site info context: $context");
            //noinspection ConstantConditions
            $siteInfo = $this->infoLoader->getSiteInfo($context, $langInfo['id']);

            //Stop everything if site info is not correct.
            if(empty($siteInfo) || sizeof($siteInfo) === 0) {
                info("loadData: Site not found!");
                return $this->getErrorMessage("Site not found", 400);
            }
            $this->cacheManager->put($contextCacheKey, $siteInfo);

        } else {
            info("loadData: From Cache ($contextCacheKey): Fetching site info, context: $context");
            $siteInfo = $this->cacheManager->get($contextCacheKey);
        }

        $site_id = $siteInfo['id'];
        app()->HashtagCms->infoLoader()->setInfoKeeper("site_id", $site_id);

        //check lang - return 404 if not found - check in lang_site table
        $langCacheKey = $context.'_'.$lang;
        if(!$this->cacheManager->exists($langCacheKey) || $clearCache) {
            info("loadData: Fetching again for lang info lang:$lang, with context: $context");
            //noinspection ConstantConditions
            $langSiteInfo = $this->infoLoader->getLangSiteInfo($lang, $site_id);

            //Stop everything if lang info is not correct.
            if(empty($langSiteInfo) || sizeof($langSiteInfo) === 0) {
                info("loadData: Lang is exist but not supported for this site");
                return $this->getErrorMessage("Lang is exist but not supported for this site", 404);
            }
            $this->cacheManager->put($langCacheKey, $langSiteInfo);

        } else {
            info("loadData: Fetching again for lang info lang:$lang, with context: $context");
            $langSiteInfo = $this->cacheManager->get($langCacheKey);
        }
        $lang_id = $langSiteInfo['id'];
        app()->HashtagCms->infoLoader()->setInfoKeeper("lang_id", $lang_id);

        //check tenant - return 404 if not found - check in site_tenant table
        $tenantCacheKey = $context.'_'.$tenant;
        if(!$this->cacheManager->exists($tenantCacheKey) || $clearCache) {
            info("loadData: Fetching tenant info tenant: $tenant, context: $context");
            //noinspection ConstantConditions
            $tenantSiteInfo = $this->infoLoader->getTenantSiteInfo($tenant, $site_id);

            //Stop everything if lang info is not correct.
            if(empty($tenantSiteInfo) || sizeof($tenantSiteInfo) === 0) {
                info("loadData: Tenant is not supported for this site");
                return $this->getErrorMessage("Tenant is not supported for this site", 400);
            }
            $this->cacheManager->put($tenantCacheKey, $tenantSiteInfo);

        } else {
            info("loadData: From Cache ($tenantCacheKey): Fetching tenant info, context: $context, tenant: $tenant");
            $tenantSiteInfo = $this->cacheManager->get($tenantCacheKey);
        }

        $tenant_id = $tenantSiteInfo['id'];
        app()->HashtagCms->infoLoader()->setInfoKeeper("tenant_id", $tenant_id);

        //check tenant - return 404 if not found - check in site_tenant table
        $propsCacheKey = $context.'_'.$tenant."_props";
        if(!$this->cacheManager->exists($propsCacheKey) || $clearCache) {
            info("loadData: Site props info tenant: $tenant, context: $context");
            //noinspection ConstantConditions
            $sitePropsInfo = $this->infoLoader->getSitePropsInfo($site_id, $tenant_id);
            $this->cacheManager->put($propsCacheKey, $sitePropsInfo);

        } else {
            info("loadData: From Cache ($propsCacheKey): Fetching tenant info, context: $context, tenant: $tenant");
            $sitePropsInfo = $this->cacheManager->get($propsCacheKey);
        }

        //check category - it could be "category or category/{link_rewrite} or category/{link_rewrite?}", ie blog, or blog/test-content
        //  ie: support/tnc
        $category_params = array();
        $categoryCacheKey = ($context.'_'.$tenant.'_'.$lang).'_'.(str_replace("/", "_",$category));
        if(!$this->cacheManager->exists($categoryCacheKey) || $clearCache) {
            info("loadData: Fetching category info category: $category, context: $context, tenant: $tenant lang: $lang");

            $fullUrl = null;

            if(str_contains($category, "/")) {
                $fullUrl = $category;
                $category_params = explode("/", $category);
                $category = array_shift($category_params);
                $category = (empty($category)) ? "/" : $category;
                // index 0 is category, rest can be link_rewrite for content based on link_rewrite_pattern field
            }

            //noinspection ConstantConditions
            $categorySiteInfo = $this->infoLoader->getCategorySiteInfo($category, $site_id, $tenant_id, $lang_id, $fullUrl);

            //Stop everything if lang info is not correct.
            if(empty($categorySiteInfo) || sizeof($categorySiteInfo) === 0) {
                info("loadData: Category not found in this site.");
                return $this->getErrorMessage("Could not find the category.", 404);
            }
            $this->cacheManager->put($categoryCacheKey, $categorySiteInfo);

        } else {
            info("loadData: From Cache ($categoryCacheKey): Fetching tenant info, context: $context, tenant: $tenant");
            $categorySiteInfo = $this->cacheManager->get($categoryCacheKey);
        }
        $category_id = $categorySiteInfo['id'];
        $link_rewrite_pattern = $categorySiteInfo['link_rewrite_pattern'];
        if(sizeof($category_params) > 0 && !empty($link_rewrite_pattern)) {

            $totalCount = preg_match_all("/\{*+\}/", $link_rewrite_pattern, $matches);
            $optionalCount = preg_match_all("/\?}/", $link_rewrite_pattern, $matches);
            $requiredCount = $totalCount - $optionalCount;

            if((sizeof($category_params) > $totalCount) || sizeof($category_params) < $requiredCount) {
                info("loadData: Dynamic url is mismatched.");
               return $this->getErrorMessage("Dynamic url is mismatched.", 400); //Bad request
            }
            //set in context
            $link_rewrite_patterns = explode("/", $link_rewrite_pattern);
            foreach ($category_params as $index=>$lr) {
                $key = preg_replace("/\{|\}|\?/", "", $link_rewrite_patterns[$index]);
                $this->infoLoader->setContextVars($key, $lr);
            }
            //@todo: Need to find a solution for this - dynamic keys (link_rewrite) with this value
            if(!empty($contentUrl)) {
                $this->infoLoader->setContextVars("link_rewrite", $contentUrl);
            }

        }

        $theme_id = $categorySiteInfo['theme_id'];
        app()->HashtagCms->infoLoader()->setInfoKeeper("theme_id", $theme_id);

        // get theme info
        $themeCacheKey = ($context.'_'.$tenant.'_'.$lang).'_'.$theme_id;
        if(!$this->cacheManager->exists($themeCacheKey) || $clearCache) {
            info("loadData: Fetching theme info theme: $theme_id");
            //noinspection ConstantConditions
            $themeInfo = $this->infoLoader->getThemeInfo($theme_id);

            //Stop everything if lang info is not correct.
            if($themeInfo === null) {
                info("loadData: Theme not found or deleted.");
                return $this->getErrorMessage("Theme not found or deleted.", 404);
            }
            $this->cacheManager->put($themeCacheKey, $themeInfo);

        } else {
            info("loadData: From Cache ($themeCacheKey): Fetching theme info theme: $theme_id");
            $themeInfo = $this->cacheManager->get($themeCacheKey);
        }
        $themeInfo = $themeInfo->toArray();

        $layoutManager = app()->HashtagCms->layoutManager();

        // need to set layout info for theme folder etc
        // parse theme

        $parsedTheme = $layoutManager->parseSkeleton($themeInfo['skeleton'], $site_id);

        $parsedHooks = $parsedTheme["hooks"];
        $parseModules = $parsedTheme["modules"];

        if(sizeof($parsedHooks) === 0 && sizeof($parseModules) === 0) {
            info("loadData: There is no hook or module in the theme.");
            return $this->getErrorMessage("There is no hook or module in the theme.", 404);
        }

        $this->infoLoader->setMultiContextVars($category_id, $site_id, $tenant_id, $microsite_id);
        // Set Language
        $this->infoLoader->setLanguageId($lang_id);

        $parsedThemeWithData = $this->infoLoader->getModuleSiteInfoDataByParsedHooksAndModules($parsedTheme, $category_id, $site_id, $tenant_id, $microsite_id);
        //dd($parsedThemeWithData);

        // set seo content


        //Check if there is any module is required and content is available
        $contentFound = $this->moduleLoader->isContentFound();
        if(!$contentFound) {
            return $this->getErrorMessage("Content not found!", 404);
        }

        //Start building data
        $data = array();
        $this->layoutManager->setThemePath($themeInfo["directory"]);

        //category header/footer
        $neg = base64_decode('PG1ldGEgbmFtZT0iZ2VuZXJhdG9yIiBuYW1lPSIjQ01TIChodHRwczovL3d3dy5oYXNodGFnY21zLm9yZy8pIj4=');
        $categorySiteInfo["header_content"] = $this->layoutManager->parseStringForPath($categorySiteInfo["header_content"]);
        $categorySiteInfo["footer_content"] = $this->layoutManager->parseStringForPath($categorySiteInfo["footer_content"]);

        //theme header/footer
        $themeInfo["header_content"] = $neg.$this->layoutManager->parseStringForPath($themeInfo["header_content"]);
        $themeInfo["footer_content"] = $this->layoutManager->parseStringForPath($themeInfo["footer_content"]);
        $themeInfo["skeleton"] = $this->layoutManager->parseStringForPath($themeInfo["skeleton"]);

        $seoContent = $this->moduleLoader->getSeoContent();

        $metaDesc = $categorySiteInfo["meta_description"];
        $metaKeywords = $categorySiteInfo["meta_keywords"];
        $metaRobots = ($categorySiteInfo["meta_robots"] == null) ? "index, follow" : $categorySiteInfo["meta_robots"];
        $metaCanonical = $categorySiteInfo["meta_canonical"];

        $isLoginRequired = $categorySiteInfo["required_login"] === 1 || $this->moduleLoader->isLoginRequired();

        //Category meta title or category title
        $categoryTitle = (empty($categorySiteInfo["meta_title"])) ? $categorySiteInfo["title"] : $categorySiteInfo["meta_title"];

        // SEO handling
        // if any module is seo module
        // fetch data from that
        // else category meta info
        // else site meta info

        if($seoContent != null) {
            $metaDesc = ($seoContent["metaDescription"] == null) ? $metaDesc : $seoContent["metaDescription"];
            $metaKeywords = ($seoContent["metaKeywords"] == null) ? $metaKeywords : $seoContent["metaKeywords"];
            $metaRobots = ($seoContent["metaRobots"] == null) ? $metaRobots : $seoContent["metaRobots"];
            $metaCanonical = ($seoContent["metaCanonical"] == null) ? $metaCanonical : $seoContent["metaCanonical"];

            $categoryTitle = ($seoContent["metaTitle"] == null) ? $categoryTitle : $seoContent["metaTitle"];

            //add seo module header/footer content
            $categorySiteInfo["header_content"] = $categorySiteInfo["header_content"].$this->layoutManager->parseStringForPath($seoContent["headerContent"] ?? "");
            $categorySiteInfo["footer_content"] = $categorySiteInfo["footer_content"].$this->layoutManager->parseStringForPath($seoContent["footerContent"] ?? "");
            $categorySiteInfo["page_active_key"] =  ($seoContent["activeKey"] == null) ? "" : $seoContent["activeKey"];

            $categorySiteInfo["page_link_rewrite"] =  ($seoContent["link_rewrite"] == null) ? "" : $seoContent["link_rewrite"];
            $categorySiteInfo["page_id"] =  ($seoContent["page_id"] == null) ? "" : $seoContent["page_id"];
            $categorySiteInfo["page_name"] =  ($seoContent["page_name"] == null) ? "" : $seoContent["page_name"];
        }

        $metaTitle = (empty($categoryTitle)) ? $siteInfo["title"] : $categoryTitle;

        $headerMeta = array(
            "description"=>$metaDesc,
            "keywords"=>$metaKeywords,
            "robots"=>$metaRobots
        );

        $metaLinks = array();
        if($metaCanonical !== null) {
            $metaLinks[] = array("rel" => "canonical", "href" => $metaCanonical);
        }
        //fav icon
        if(isset($siteInfo['favicon']) && !empty(trim($siteInfo['favicon']))) {
            $metaLinks[] = array("rel"=>"shortcut icon", "href"=>htcms_get_media($siteInfo['favicon']));
        } else {
            //add default icon
            $metaLinks[] = array("rel"=>"shortcut icon", "href"=>htcms_get_image_resource("favicon.png"));
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
        $data['html'] = array(
                            "head"=> array("links"=>$metaLinks,
                                "headerContent"=>$themeInfo["header_content"].$categorySiteInfo['header_content'],
                                "title"=>$metaTitle,
                                "meta"=> $headerMeta,
                                "metaContent"=>$metaContent
                                ),
                            "body"=> array("content"=>$themeInfo['skeleton'],
                                "footer"=>array("footerContent"=>$themeInfo["footer_content"].$categorySiteInfo['footer_content'],)),
                            );

        $data['meta'] = array(
            "site"=>$siteInfo,
            "category"=>$categorySiteInfo,
            "lang"=>$langInfo,
            "theme"=> array_merge($themeInfo, array("hooks"=>$parsedThemeWithData['hooks'], "modules"=>$parsedThemeWithData['modules'])),
            "tenant"=>$tenantSiteInfo,
            "siteProps"=>$sitePropsInfo
        );
        $data['isLoginRequired'] = $isLoginRequired;
        $data['status'] = 200;

        return $data;

    }

    /**
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
