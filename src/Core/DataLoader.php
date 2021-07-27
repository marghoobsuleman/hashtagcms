<?php


namespace MarghoobSuleman\HashtagCms\Core;

use MarghoobSuleman\HashtagCms\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Class DataLoader
 * @package MarghoobSuleman\HashtagCms\Core
 * @version 1.2
 */

class DataLoader
{

    private $common;
    protected $defaultController = "frontend";
    protected $defaultMethod = "index";

    public function __construct()
    {
        $this->common = app()->Common;

    }

    /**
     * Load Data
     *
     * @param string|null $category
     * @param string $language
     * @param string $tenant_link_rewrite
     * @param string|null $site_context
     * @param int $microsite_id
     * @param bool $dynamicUrlCheck
     * @return array
     */
    public function loadData(string $category = null, string $language = 'en', string $tenant_link_rewrite = 'web',
                             string $site_context = null, int $microsite_id = 0, $dynamicUrlCheck = true) {

        $linkRewrites = [];
        $categoryName = $realLinkName = $category;
        $realLinkName = str_replace("//", "/", $realLinkName);
        if(Str::contains($categoryName, "/")) {
            $categoryName = str_replace("//", "/", $categoryName);
            $linkRewrites = explode("/", $categoryName);
            $categoryName = ($linkRewrites[0] == "") ? "/" : $linkRewrites[0];
            array_shift($linkRewrites); //remove first
            $linkRewrites = ($linkRewrites[0] == "index" && sizeof($linkRewrites) == 1) ? [] : $linkRewrites;
        }

        info("DataLoader->loadData:: category: $category, language: $language, tenant_link_rewrite: $tenant_link_rewrite, 
                        site_context: $site_context, microsite_id: $microsite_id");

        //Get tenants
        $tenant = $this->getTenantByLinkrewrite($tenant_link_rewrite);

        if($tenant == null || sizeof($tenant) == 0) {
            //return array("error"=>404, "message"=>"Tenant is not defined");
            info(array("error"=>404, "message"=>"DataLoader:: Tenant is not defined"));
            return $this->sendError("DataLoader:: Tenant is not defined");
        }

        $tenant_id = $tenant["id"];

        //Get Language
        $langs = $this->getLanguageByLinkrewrite($language);

        if($langs == null || sizeof($langs) == 0) {
            info(array("error"=>404, "message"=>"DataLoader:: Language is not defined"));
            return $this->sendError("DataLoader:: Language is not defined");
        }
        $lang_id = $langs["id"];


        //Get Site
        //$site = $this->getSiteByContext($site_context, $lang_id, $tenant_id);
        $site = $this->getSiteByContext($site_context, $lang_id);

        if($site == null || sizeof($site) == 0) {
            info(array("error"=>404, "message"=>"DataLoader:: Context, language or tenant combination not found"));
            return $this->sendError("DataLoader:: Context, language or tenant combination not found");
        }

        $site_id = $site["id"];

        //Some check for root.
        if($categoryName == "/") {
            if($site["default_category_id"] != null) {
                $categoryName = $site["default_category_id"]; //from site category
            }
        }


        //check if category is an id - get link rewrite by id;
        if(is_numeric($categoryName)) {
            $catObj = Category::withoutGlobalScopes()->where("id", "=", $categoryName)->first("link_rewrite");
            if($catObj!=null) {
                $categoryName = $catObj->link_rewrite;
            }

        }

        info("Getting data for::  categoryName: $categoryName, site_id: $site_id, tenant_id: $tenant_id, lang_id:$lang_id, realLinkName: $realLinkName" );

        //Get Category
        $category = $this->getCategoryByLinkrewrite($categoryName, $site_id, $tenant_id, $lang_id, $realLinkName);

        $totalCategoryCount = ($category == null || sizeof($category) == 0) ? 0 : sizeof($category); //since null check is required

        //if category not found
        if($totalCategoryCount == 0) {
            info(array("error"=>404, "message"=>"DataLoader:: Category [$categoryName] not found"));
            return $this->sendError("DataLoader:: Category [$categoryName] not found");
        } else {
            info("DataLoader:: Found category data");
        }

        //if category has more than one with same link, such as content/{link_rewrite} or content/about-us
        //find if one has an absolute url else use dynamic one
        $foundInFullUrl = false;
        $category = $this->getOneCategory($category, $realLinkName);

        if($totalCategoryCount > 1 || isset($category["link_rewrite"])) {

            $foundInFullUrl = true; //it will not check controller and method strictly

        } else {
            //Check if this is a plain object array or array of an array
            $category = isset($category["link_rewrite"]) ? $category : $category[0];
        }

        //Safe side when default site category is changed
        $linkRewrites[0] = $category["link_rewrite"];

        $category_id = $category["id"];


        //Check category dynamic url
        if($dynamicUrlCheck) {
            $realLinkNameArr = explode("/", $realLinkName);

            //This is specially for checking dynamic url pattern - it is not being used when you call from api
            if(isset($category["link_rewrite_pattern"])) {

                array_shift($realLinkNameArr);
                $link_rewrite_pattern = $category["link_rewrite_pattern"];

                if($link_rewrite_pattern != "" && $link_rewrite_pattern != null) {

                    //Calculate required link count
                    $totalCount = preg_match_all("/\{*+\}/", $link_rewrite_pattern, $matches);
                    $optionalCount = preg_match_all("/\?}/", $link_rewrite_pattern, $matches);
                    $requiredCount = $totalCount - $optionalCount;

                    // echo "totalCount, optionalCount, requiredCount, sizeof linkRewrites, linkRewrites";

                    if((sizeof($realLinkNameArr) > $totalCount) || sizeof($realLinkNameArr) < $requiredCount) {
                        info(array("error"=>404, "message"=>"DataLoader:: Dynamic url is mismatched"));
                        return $this->sendError("DataLoader:: Dynamic url is mismatched");

                    } else {

                        $link_rewrite_patterns = explode("/", $link_rewrite_pattern);
                        foreach ($realLinkNameArr as $index=>$lr) {
                            $key = preg_replace("/\{|\}|\?/", "", $link_rewrite_patterns[$index]);
                            $this->common->setContextVariable($key, $realLinkNameArr[$index]);
                        }

                    }

                }
            } else {

                //check if has a controller and method
                //if yes - let it process
                //else - throw an error
                $controllerName = ($realLinkNameArr[0] == "/" || $realLinkNameArr[0]=="") ? $this->defaultController : $realLinkNameArr[0];
                $methodName = ($realLinkNameArr[1] == "") ? $this->defaultMethod : $realLinkNameArr[1];

                //reality check for controller and method
                $namespace = config("hashtagcms.namespace");
                //	* if class exist controllername is blog else it’s frontend
                //	* if method exist method name is story else it’s index
                $callable = $namespace."Http\Controllers\\".str_replace("-", "", Str::ucfirst($controllerName))."Controller";
                $callableDefault = $namespace."Http\Controllers\\".str_replace("-", "", Str::ucfirst($this->defaultController))."Controller";

                $foundController = false;
                $foundMethod = false;
                info("DataLoader:: callable: ".$callable);
                info("DataLoader:: callableDefault: ".$callableDefault);
                info("DataLoader:: methodName: ".$methodName);
                //Check if controller has a method.
                if(class_exists($callable) && method_exists($callable, $methodName)) {
                    $foundController = $foundMethod = true;
                } else {
                    //check controller in frontend controller
                    if(class_exists($callableDefault) && method_exists($callableDefault, $methodName)) {
                        $foundController = $foundMethod = true;
                    }
                }
                info("DataLoader:: foundController: $foundController, foundMethod: $foundMethod, foundInFullUrl: $foundInFullUrl");
                $exist = ((($foundController && $foundMethod) || $foundInFullUrl));

                if(!$exist) {
                    info(array("error"=>404, "message"=>"DataLoader:: Dynamic url is mismatched"));
                    return $this->sendError("DataLoader:: Dynamic url is mismatched");
                }

            }

        }

        //Set Global Variables
        $this->common->setContextVars($category_id, $site_id, $tenant_id, $microsite_id);
        $this->common->setLanguageId($lang_id);



        $data["tenant"] = $tenant;
        $data["langs"] = $langs;
        $data["site"] = $site;
        $data["hasTheme"] = false;
        //theme
        $theme = $this->common->dbSelectOne("select cs.category_id, cs.site_id, cs.tenant_id, cs.theme_id, cs.icon_css, cs.cache_category,
                                            th.name as theme_name, th.directory, th.body_class, th.skeleton, th.header_content, th.footer_content
                                            from category_site cs
                                            left join themes th on (cs.theme_id = th.id)
                                            where cs.category_id=:category_id AND cs.tenant_id=:tenant_id and cs.site_id=:site_id");

        //No theme is defined
        if(empty($theme["directory"])) {
            info(array("error"=>404, "message"=>"DataLoader:: Theme not found"));
            return $this->sendError("DataLoader:: Theme not found", 404, $data);
            //return $data;
        }

        //Set theme_path
        $this->common->setThemePath($theme["directory"]);
        $data["hasTheme"] = true;

        //category header/footer
        $neg = base64_decode('PG1ldGEgbmFtZT0iZ2VuZXJhdG9yIiBuYW1lPSIjQ01TIChodHRwczovL3d3dy5oYXNodGFnY21zLm9yZy8pIj4=');
        $category["header_content"] = $this->common->parseStringForPath($category["header_content"]);
        $category["footer_content"] = $this->common->parseStringForPath($category["footer_content"]);

        //theme header/footer
        $theme["header_content"] = $neg.$this->common->parseStringForPath($theme["header_content"]);
        $theme["footer_content"] = $this->common->parseStringForPath($theme["footer_content"]);
        $theme["skeleton"] = $this->common->parseStringForPath($theme["skeleton"]);

        info("found category, site, theme etc");

        //Load Modules
        $moduleLoader = new ModuleLoader();
        $modules = $moduleLoader->getModules($category_id, $site_id, $tenant_id, $microsite_id);


        //Fetch if there is any seo content
        $seoContent = $moduleLoader->getSeoContent();

        $html = array();
        $header = array();

        info("category_id: $category_id, site_id: $site_id, tenant_id: $tenant_id, microsite_id: $microsite_id");

        //Category meta
        $metaDesc = $category["meta_description"];
        $metaKeywords = $category["meta_keywords"];
        $metaRobots = ($category["meta_robots"] == null) ? "index, follow" : $category["meta_robots"];
        $metaCanonical = $category["meta_canonical"];


        //Category meta title or category title
        $categoryTitle = (empty($category["meta_title"])) ? $category["title"] : $category["meta_title"];

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
            $category["header_content"] = $category["header_content"].$this->common->parseStringForPath($seoContent["headerContent"] ?? "");
            $category["footer_content"] = $category["footer_content"].$this->common->parseStringForPath($seoContent["footerContent"] ?? "");
            $category["page_active_key"] =  ($seoContent["activeKey"] == null) ? "" : $seoContent["activeKey"];
        }

        $header["title"] = (empty($categoryTitle)) ? $site["title"] : $categoryTitle;

        $header["meta"] = array(
            "description"=>$metaDesc,
            "keywords"=>$metaKeywords,
            "robots"=>$metaRobots
        );

        $header["link"] = array(array("rel"=>"canonical", "href"=>$metaCanonical));

        //fav icon
        if(isset($site['favicon']) && !empty(trim($site['favicon']))) {
            array_push($header["link"],array("rel"=>"shortcut icon", "href"=>htcms_get_media($site['favicon'])));
        } else {
            //add default icon
            array_push($header["link"],array("rel"=>"shortcut icon", "href"=>htcms_get_image_resource("favicon.png")));
        }


        //Check if login is required
        $isLoginRequired = ($category["required_login"] == 1 || $moduleLoader->isLoginRequired()) ? true : false;

        //Check if there is any module is required and content is available
        $contentFound = $moduleLoader->isContentFound();

        //info("============================ category ===============");
        //info($category);
        //Set some info for future use
        $this->common->setInfo('site', $site);
        $this->common->setInfo('language', $langs);
        $this->common->setInfo('tenant', $tenant);
        $this->common->setInfo('theme', $theme);
        $this->common->setInfo('category', $category);

        $data["category"] = $category;
        $html["header"] = $header;
        $html["theme"] = $theme;
        $html["theme"]["hooks"] = $modules;
        $html["totalModules"] = $moduleLoader->getModulesCount();
        $data["html"] = $html;
        $data["isLoginRequired"] = $isLoginRequired;


        if($dynamicUrlCheck) {
            if($contentFound) {
                $data["status"] = 200;
            } else {
                return $this->sendError("DataLoader:: Content not found!", 404, $data);
            }
        }

        return $data;
    }

    /**
     * Get Site by context
     * @param string $context
     * @param int $lang_id
     * @param int $tenant_id
     * @return mixed
     */
    private function getSiteByContext(string $context, int $lang_id) {

        $query = "select s.id, s.name, sl.title, s.category_id as default_category_id, s.tenant_id as default_tenant_id, s.lang_id as default_lang_id, s.country_id as default_country_id,
                    s.under_maintenance, s.domain, s.context, s.favicon, s.lang_count                   
                    from sites s                    
                    left join site_langs sl on (sl.site_id = s.id)
                    where s.context = :context and sl.lang_id=:lang_id                    
                    and s.deleted_at is null";

        $data = $this->common->dbSelectOne($query, array("context"=>$context, "lang_id"=>$lang_id));

        return $data;

    }

    /**
     * Get Tenant by Link rewrite
     * @param string $tenant
     * @return mixed
     */
    private function getTenantByLinkrewrite(string $tenant='web') {

        $query = "select id, name, link_rewrite from tenants where link_rewrite=:link_rewrite and deleted_at is null";
        $result = $this->common->dbSelectOne($query, array("link_rewrite"=>$tenant));

        return $result;
    }

    /**
     * Get Language
     * @param string $lang
     * @return mixed
     */
    private function getLanguageByLinkrewrite(string $lang='en') {

        $query = "select id, name, iso_code, language_code, date_format_lite, date_format_full, is_rtl
                from langs where iso_code=:iso_code and deleted_at is null";

        $result = $this->common->dbSelectOne($query, array("iso_code"=>$lang));

        return $result;

    }

    /**
     * Get Category
     * @param string $category
     * @param int $site_id
     * @param int $tenant_id
     * @return mixed
     */
    private function getCategoryByLinkrewrite(string $category, int $site_id, int $tenant_id, int $lang_id=1, $fullUrl=null) {

        $query = "select c.id, c.required_login, c.parent_id, c.site_id, c.is_site_default, c.is_root_category, c.is_new, c.has_wap,
        c.wap_url, c.link_rewrite, c.link_navigation, c.link_rewrite_pattern, c.has_some_special_module,c.read_count,
        cs.site_id, cs.tenant_id, cs.theme_id, cs.icon_css, cs.header_content, cs.footer_content, cs.cache_category,
        cl.name, cl.title, cl.excerpt, cl.content, cl.active_key,
        cl.third_party_mapping_key, cl.b2b_mapping, cl.is_external, cl.link_relation, cl.target,
        cl.meta_title, cl.meta_keywords, cl.meta_description, cl.meta_robots, cl.meta_canonical, c.created_at, c.updated_at
        from categories c
        left join category_site cs on(cs.category_id = c.id)
        left join category_langs cl on(c.id = cl.category_id)
        where cs.tenant_id=:tenant_id and cs.site_id=:site_id and c.publish_status=1 and c.deleted_at is null and cl.is_external=0 and cl.lang_id=:lang_id and ";

        if($fullUrl != null && !empty($fullUrl)) {
            $query .= " (";
        }
        $query .= " c.link_rewrite =:link_rewrite ";
        if($fullUrl != null && !empty($fullUrl)) {
            $query .= " OR c.link_rewrite = '$fullUrl' )";
        }

        //info($query);

        $res = $this->common->dbSelect($query, array("link_rewrite"=>$category, "site_id"=>$site_id, "tenant_id"=>$tenant_id, "lang_id"=>$lang_id));

        return json_decode(json_encode($res), true);
    }


    /**
     * @param string $message
     * @param int $status
     * @param array $withData
     * @return array
     */
    private function sendError($message='', $status=404, $withData=[]) {
        $error = array("message"=>$message, "status"=>$status);
        return array_merge($error, $withData);
    }

    /**
     * Fetch one which is more on priority
     * Find full url(/abc/xyz/zyd) if not try to find with link_rewrite from the category
     * @param array $data
     * @param string $link_rewrite
     * @return array|mixed]
     */
    private function getOneCategory(array $data, $link_rewrite='') {
        for($i=0;$i<count($data);$i++) {
            if($link_rewrite == $data[$i]["link_rewrite"]) {
                return $data[$i];
            }
        }
        return $data;
    }



}

