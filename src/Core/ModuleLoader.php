<?php


namespace MarghoobSuleman\HashtagCms\Core;


use MarghoobSuleman\HashtagCms\Core\Utils\Result;
use MarghoobSuleman\HashtagCms\Models\Hook;
use MarghoobSuleman\HashtagCms\Models\Module;
use Illuminate\Support\Facades\Http;

/**
 * Class ModuleLoader
 * @package MarghoobSuleman\HashtagCms\Core
 * @version 1.3
 */
class ModuleLoader
{

    private $hookPlaceHolder = "hook_";
    private $modulePlaceHolder = "module_";
    private $totalModules = 0;

    private $globalKeyMap = array();
    private $common;
    private $seoModuleInfo = array();
    private $foundSeoModule = false;
    private $loginRequired = false;
    private $contentFound = true;

    private static $mandatoryModuleCheck = true;

    public function __construct()
    {
        $this->common = app()->Common;
    }


    /**
     * Get category modules
     * @param int $category_id
     * @param int $site_id
     * @param int $tenant_id
     * @param int $microsite_id
     * @return mixed
     */
    public function getCategoryModules(int $category_id, int $site_id, int $tenant_id, int $microsite_id=0, int $hook_id=null) {

        $withHook = ($hook_id != null) ? " AND ms.hook_id=$hook_id" : "";

        return $this->common->dbSelect("select ms.module_id, ms.hook_id, ms.position,
        m.name, m.alias, m.is_seo_module, m.linked_module, m.view_name, m.data_type, m.query_statement, m.shared,
        m.query_as, m.data_handler, m.data_key_map, m.is_mandatory,
        m.method_type, m.service_params, m.individual_cache, m.cache_group
        from module_site ms
        left join modules m on (ms.module_id = m.id)
        where ms.category_id = $category_id AND ms.site_id = $site_id
        AND ms.tenant_id = $tenant_id AND ms.microsite_id = $microsite_id $withHook
        order by ms.hook_id, ms.position ASC");
    }



    /**
     * Get Modules by category and return data in html array kind of.
     * It conists all info like category, theme, hooks and modules with data
     * @param int $category_id
     * @param int $site_id
     * @param int $tenant_id
     * @param int $microsite_id
     * @return array
     */
    public function getModules(int $category_id, int $site_id, int $tenant_id, int $microsite_id=0):array {

        $query = array("category_id"=>(int)$category_id, "site_id"=>(int)$site_id, "tenant_id"=>(int)$tenant_id);
        $queryWithMicrosite = array("category_id"=>(int)$category_id, "site_id"=>(int)$site_id, "tenant_id"=>(int)$tenant_id, "microsite_id"=>0);

        //Get Theme and Category
        $theme = $this->common->dbSelect("select cs.category_id, c.link_rewrite_pattern, cs.site_id, cs.tenant_id, cs.theme_id, cs.icon_css, cs.cache_category,
th.name as theme_name, th.directory, th.body_class, th.skeleton, th.header_content, th.footer_content
from category_site cs
left join themes th on (cs.theme_id = th.id)
left join categories c on (c.id = cs.category_id)
where cs.category_id=$category_id AND cs.tenant_id=$tenant_id and cs.site_id=$site_id");

        info("ModuleLoader.getModules ========= Theme ==========");
        //info(json_encode($theme[0]));
        //dd($theme);
        if($theme == null || sizeof($theme) ==0) {
            return array("error"=>true, "message"=>"I guess, there is no theme defined for this category");
        }

        //Is category has link pattern such as (link/{pattern})
        $link_rewrite_pattern = $theme[0]->link_rewrite_pattern;

        if($link_rewrite_pattern !== "" || $link_rewrite_pattern !=NULL) {
            info("link_rewrite_pattern: ".$link_rewrite_pattern);
        }

        //Parse Hooks and info
        $hooksInfo = $this->parseSkelton($theme[0]->skeleton);

        //info(json_encode($hooksInfo));

        //Fetch Category Data from module_site table with module

        //Get all modules from this category
        $categoryModules = $this->getCategoryModules($category_id, $site_id, $tenant_id, $microsite_id);
        //dd($categoryModules);

        info("total modules: ".sizeof($categoryModules));

        $totalModules = sizeof($categoryModules);
        $environment = env("APP_ENV");

        if($totalModules > 0) {
            //Load module data
            foreach ($categoryModules as $key=>$module) {
                $hookPlaceholder = $this->hookPlaceHolder.$module->hook_id;
                $moduleData = null;

                //create module key if not defined
                if(!isset($hooksInfo[$hookPlaceholder]["modules"])) {
                    $hooksInfo[$hookPlaceholder]["modules"] = array();
                }

                try {
                    $moduleData = $this->getModuleData($module);
                    $m =  array("name"=> $module->name,
                        "alias"=>$module->alias,
                        "view_name"=> $module->view_name,
                        "data_type"=> $module->data_type,
                        "is_seo_module"=>$module->is_seo_module,
                        "data"=>$moduleData,
                        "individual_cache"=> $module->individual_cache,
                        "position"=> $module->position);

                        //Better to hide query when using prod
                        if($environment !== "prod") {
                            $m["data_handler"] = $module->data_handler;
                        }

                    //store module data
                    $hooksInfo[$hookPlaceholder]["modules"][] = $m;

                } catch (\Exception $e) {
                    info("Error in fetching module: ".$module->alias . " Messagea: ".$e->getMessage());
                }

            }
        }


        /*$data = array("theme"=>$theme,
            "hooks"=>$hooksInfo,
            "totalModules"=>$totalModules
        );*/

      // dd("ModuleLoader: hooksInfo ", $hooksInfo);

        return $hooksInfo;
    }

    /**
     * Get query module
     * @param string $query
     * @return array
     */
    public function getQueryModule(string $query) {
        return Result::forView($this->common->dbSelect($query));
    }

    /**
     * Get service module
     * @param string $service_url
     * @param string $method_type
     * @param array $withData
     * @return mixed|null
     */
    public function getServiceModule(string $service_url, string $method_type, array $withData=array()) {

        $data = null;
        if($service_url == "" || $service_url == null) {
            return null;
        }


        $urls = explode("?", $service_url);

        $arguments = array();

        //make sure if we are not passing data
        if(sizeof($urls)>1 && sizeof($withData) == 0) {
            //we have arguments too
            parse_str($urls[1], $arguments);
        }
        $url = $urls[0];
        $arguments = array_merge($arguments, $withData);


        switch (strtolower($method_type)) {
            case "get":
                $data = Http::get($url, $arguments)->json();
                break;
            case "post":
                $data = Http::post($url, $arguments)->json();
                break;
        }

        return Result::forView($data);
    }
    //'Static','Query','Service','Custom','QueryService','UrlService'
    /**
     * Get module from a service URL. It also parse all query params and send it the service url
     * @param string $service_url
     * @param string $method_type
     * @param string $data_key_map (this will be comma separated)
     * @return array|null
     */
    public function getUrlServiceModule(string $service_url, string $method_type, string $data_key_map) {
        $data = null;

        if($service_url == "" || $service_url == null) {
            return null;
        }
        $urls = explode("?", $service_url);
        $url = $urls[0];
        $arguments = $this->makeQueryParams($data_key_map);

        return  $this->getServiceModule($url, $method_type, $arguments);

    }

    /**
     * Get query service module
     * if query as data: call service, execute query and return datas
     * else if query is as param: execute query, replace query with servie url param/value and call service and return data
     * @param string $query
     * @param string $serviceUrl
     * @param string $query_as
     * @param string $method_type
     * @return array
     */
    public function getQueryServiceModule(string $query, string $serviceUrl, string $query_as, string $method_type) {
        $data = array();

        if($query_as == "data" || $query_as == "") {
            //we should retrun the data
            $data["serviceData"] = $this->getServiceModule($serviceUrl, $method_type);
            $data["queryData"] = $this->getQueryModule($query);

        } else {

            $data2 = $this->getQueryModule($query);
            $arguments = array();
            $arguments = json_decode(json_encode($data2), true)[0]; //kind fo toArray()

            $data["queryData"] = $data2;
            $data["serviceData"] = $this->getServiceModule($serviceUrl, $method_type, $arguments);

        }
        return $data;
    }

    /**
     * Get Static Module
     * Fetch data from a content table
     * @param string $alias
     * @return array
     */
    public function getStaticModule(string $alias) {
        $query = "select smcl.title, smcl.content from static_module_contents smc  
                  left join static_module_content_langs smcl on (smc.id = smcl.static_module_content_id)
                  where smc.alias='$alias' and lang_id=:lang_id and site_id=:site_id";

        return Result::forView($this->common->dbSelectOne($query));

    }

    /**
     * Get Module Data
     * It handles all type of module
     * @param $module_obj
     * @return string|array
     */
    public function getModuleData($module_obj) {
        $dataType = $module_obj->data_type;
        $dataHandler = ($module_obj->data_handler == "" || $module_obj->data_handler==null) ? "" : $module_obj->data_handler;
        $methodType = $module_obj->method_type;
        $is_seo_module = $module_obj->is_seo_module;
        $is_mandatory = $module_obj->is_mandatory;
        $is_shared = $module_obj->shared;
        $data = null;
        $data2 = null;

        info("dataHandler: ".$dataHandler);
        switch (strtolower($dataType)) {

            case "query":

                $data = $this->getQueryModule($dataHandler);

                break;

            case "service":

                $data = $this->getServiceModule($dataHandler, $methodType);

                break;

            case "urlservice":

                $data = $this->getUrlServiceModule($dataHandler, $methodType, $module_obj->data_key_map);

                break;

            case "queryservice":

                $data = $this->getQueryServiceModule($module_obj->query_statement, $dataHandler, $module_obj->query_as, $methodType);

                break;

            case "static":

                $data = $this->getStaticModule($dataHandler);

                break;

            case "custom":
                //will do something
                $data = array();
                break;

            default:

                break;
        }

        //Save seo info
        if($is_seo_module == 1 && $this->foundSeoModule == false) {

            $forSeo = (array) json_decode(json_encode($data),true);
            $forSeo = (sizeof($forSeo) >= 1) ? $forSeo[0] : $forSeo;

            if(sizeof($forSeo) > 0) {
                info("=== Set this data for seo module === ");
                //info(json_encode($forSeo));
                $this->foundSeoModule = true;
                $active_key = (isset($forSeo["active_key"]) && !empty($forSeo["active_key"])) ? $forSeo["active_key"] : null;
                $page_link_rewrite = (isset($forSeo["link_rewrite"]) && !empty($forSeo["link_rewrite"])) ? $forSeo["link_rewrite"] : null;
                $meta_title = (isset($forSeo["meta_title"]) && !empty($forSeo["meta_title"])) ? $forSeo["meta_title"] : null;
                $meta_keywords = (isset($forSeo["meta_keywords"]) && !empty($forSeo["meta_keywords"])) ? $forSeo["meta_keywords"] : null;
                $meta_description = (isset($forSeo["meta_description"]) && !empty($forSeo["meta_description"])) ? $forSeo["meta_description"] : null;
                $meta_robots = (isset($forSeo["meta_robots"]) && !empty($forSeo["meta_robots"])) ? $forSeo["meta_robots"] : null;
                $meta_canonical = (isset($forSeo["meta_canonical"]) && !empty($forSeo["meta_canonical"])) ? $forSeo["meta_canonical"] : null;

                $this->seoModuleInfo = array(
                    "metaTitle"=>$meta_title,
                    "metaKeywords"=> $meta_keywords,
                    "metaDescription"=>$meta_description,
                    "metaRobots"=>$meta_robots,
                    "metaCanonical"=>$meta_canonical,
                    "activeKey"=>$active_key,
                    "link_rewrite"=>$page_link_rewrite,
                    "page_id"=>$forSeo["id"],
                    "page_name"=>$forSeo["name"]
                );

                if(isset($forSeo["header_content"])) {
                    $this->seoModuleInfo["headerContent"] = (isset($forSeo["header_content"]) && !empty($forSeo["header_content"])) ? $forSeo["header_content"] : "";
                    $this->seoModuleInfo["footerContent"] = (isset($forSeo["footer_content"]) && !empty($forSeo["footer_content"])) ? $forSeo["footer_content"] : "";
                }
            }
        }

        // Is there any module that requires login?
        // let check it
        if(isset($data[0]) && isset($data[0]->required_login) && $data[0]->required_login == 1) {
            info("Login is required");
            $this->loginRequired = true;
        }

        // Is there any module is required in a category?
        // lets check it
        if(($is_mandatory == 1 && sizeof($data)==0) && self::getMandatoryCheck()) {
            //dd("Mandatory content is missing");
            $this->contentFound = false;
        }

        //increase module count
        $this->totalModules++;

        //Check if this is sharable
        if($is_shared == 1) {
            $this->common->setSharedModuleData($module_obj->alias, $data);
        }
        return $data;
    }

    /**
     * This is set when you call getModules();
     * @return int
     */
    public function getModulesCount() {
        return $this->totalModules;
    }


    /**
     * Parse skeleton and return hooks data
     * @param string $skeleton
     * @return array
     */
    public function parseSkelton($skeleton='') {
        $subject = $skeleton;
        $pattern = "/\%.*?\%/";
        preg_match_all($pattern, $subject, $matches); //PREG_OFFSET_CAPTURE
        $hooksData = array();
        if(count($matches)>0) {
            $matches = $matches[0];
            foreach($matches as $key=>$val) {
                $current = $val;
                $isHook = (strpos($current, "%{cms.hook.")!==FALSE) ? TRUE : FALSE;
                $isModule = (strpos($current, "%{cms.module.")!==FALSE) ? TRUE : FALSE;
                if($isHook===TRUE) {
                    $patterns = array();
                    $patterns[0] = '/%/';
                    $patterns[1] = '/{cms.hook./';
                    $patterns[2] = '/}/';
                    $replacements = array();
                    $replacements[2] = '';
                    $replacements[1] = '';
                    $replacements[0] = '';
                    $name = preg_replace($patterns, $replacements, $current);
                    $info = $this->getHookInfo($name);
                    //$hooksData[$name] = array("name"=>$name, "type"=>"hook", "info"=>$info);
                    $placeHolder = $this->hookPlaceHolder.$info["id"];
                    $hooksData[$placeHolder]["info"] = $info;
                    $hooksData[$placeHolder]["type"] = "hook";
                    $hooksData[$placeHolder]["modules"] = [];
                  }
                if($isModule===TRUE) {
                    $patterns = array();
                    $patterns[0] = '/%/';
                    $patterns[1] = '/{cms.module./';
                    $patterns[2] = '/}/';
                    $replacements = array();
                    $replacements[2] = '';
                    $replacements[1] = '';
                    $replacements[0] = '';
                    $name = preg_replace($patterns, $replacements, $current);
                    $info = $this->getModuleInfo($name, false);
                    $placeHolder = $this->modulePlaceHolder.$info->id;
                    $hooksData[$placeHolder]["type"] = "module";
                    $hooksData[$placeHolder]["data_type"] = $info->data_type;
                    $hooksData[$placeHolder]["view_name"] = $info->view_name;
                    $hooksData[$placeHolder]["data"] = $this->getModuleData($info);
                    $hooksData[$placeHolder]["info"] = json_decode(json_encode($info), true);

                }
            }
        }
        return $hooksData;
    }


    /**
     * Get hook info
     * @param string $alias
     * @return mixed
     */
    public function getHookInfo($alias='') {
        return Hook::where('alias', $alias)->first()->toArray();
    }

    /**
     * Get module info by alias
     * @param string $alias
     * @return mixed
     */
    public function getModuleInfo(string $alias='', bool $asArray=true) {

        $where = array(array("alias", "=", $alias), array("site_id", "=", htcms_get_site_id()));
        $moduleInfo = Module::withoutGlobalScopes()->where($where)->first();

        if($asArray == true) {
            return $moduleInfo->toArray();
        }
        return $moduleInfo;
    }


    /**
     * If any module is enabled for seo
     * @return array|null
     */
    public function getSeoContent() {

        if($this->foundSeoModule) {
            return $this->seoModuleInfo;
        }
        return null;
    }

    /**
     * Check if login is required
     * @return bool
     */
    public function isLoginRequired() {
        return $this->loginRequired;
    }


    /**
     * check if content is found
     * This works if module is flag as is_mandatory=1
     * and return data is zero
     * @return bool
     */
    public function isContentFound() {
        return $this->contentFound;
    }

    /**
     * Check module mandatory
     * @param bool $checkMandatory
     */
    public static function setMandatoryCheck($checkMandatory=true) {
        self::$mandatoryModuleCheck = $checkMandatory;
    }

    /**
     * Get mandatory check
     * @return bool
     */
    public static function getMandatoryCheck() {
        return self::$mandatoryModuleCheck;
    }


    /**
     * Load module by alias
     * @param string $alias
     * @return array|string
     */
    public function loadModuleByModuleAlias(string $alias='') {

        $info = $this->getModuleInfo($alias, false);
        $data = $this->getModuleData($info);

        return $data;
    }


    /**
     * @param string $url
     * @param string $data_key_map
     * @return array
     */
    private function makeQueryParams(string $data_key_map) {
        $dataKeyMap = explode(",", $data_key_map);
        $arr = array();
        foreach ($dataKeyMap as $key=>$val) {
            $arr[$val] = request()->input($val);
        }
        return $arr;

    }


}
