<?php


namespace MarghoobSuleman\HashtagCms\Core\Main;

use MarghoobSuleman\HashtagCms\Models\Module;

/**
 * Class ModuleLoader
 * @package MarghoobSuleman\HashtagCms\Core
 * @version 1.4
 */
class ModuleLoader
{


    private int $totalModules = 0;
    private mixed $common;
    private array $seoModuleInfo = array();
    private bool $foundSeoModule = false;
    private bool $loginRequired = false;
    private bool $contentFound = true;
    private array $sharedModuleData = array();

    private static bool $mandatoryModuleCheck = true;

    public function __construct()
    {
        $this->common = app()->HashtagCms;
    }

    /**
     * Get query module
     * @param string $query
     * @return array
     */
    public function getQueryModule(string $query): array
    {
        $ml = new QueryModuleLoader($query);
        return $ml->getResult();
    }

    /**
     * Get service module
     * @param string $service_url
     * @param string $method_type
     * @param array $withData
     * @return mixed|null
     */
    public function getServiceModule(string $service_url, string $method_type, array $withData=array()): mixed
    {

        $ml = new ServiceModuleLoader($service_url, $method_type, $withData);
        return $ml->getResult();
    }
    //'Static','Query','Service','Custom','QueryService','UrlService'
    /**
     * Get module from a service URL. It also parse all query params and send it the service url
     * @param string $service_url
     * @param string $method_type
     * @param string $data_key_map (this will be comma separated)
     * @return array|null
     */
    public function getUrlServiceModule(string $service_url, string $method_type, string $data_key_map): ?array
    {
        $ml = new UrlServiceModuleLoader($service_url, $method_type, $data_key_map);
        return $ml->getResult();
    }

    /**
     * Get query service module
     * if query as data: call service, execute query and return datas
     * else if query is as param: execute query, replace query with servie url param/value and call service and return data
     * @param string $query
     * @param string $serviceUrl
     * @param string $query_as
     * @param string $method_type
     * @return array|null
     */
    public function getQueryServiceModule(string $query, string $serviceUrl, string $query_as, string $method_type): ?array
    {
        $ml = new QueryServiceModuleLoader($query, $serviceUrl, $query_as, $method_type);
        return $ml->getResult();
    }

    /**
     * Get Static Module
     * Fetch data from a content table
     * @param string $alias
     * @return array|null
     */
    public function getStaticModule(string $alias):?array {
        $ml = new StaticModuleLoader($alias);
        return $ml->getResult();
    }

    /**
     * Get Module Data
     * It handles all type of module
     * @param $module_obj
     * @return array|string|null
     */
    public function getModuleData($module_obj): array|string|null
    {
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
            info("module_obj->site_id ". json_encode($module_obj));
            $this->setSharedModuleData($module_obj->alias, $data);
        }
        return $data;
    }

    /**
     * Set shared module data
     * @param string $alias
     * @param $data
     * @return void
     */
    public function setSharedModuleData(string $alias, mixed $data):void {
        $alias = $alias."_".htcms_get_site_id();
        $this->sharedModuleData[$alias] = $data;
    }

    /**
     * Get shared module data
     * @param string $alias
     * @return mixed
     */
    public function getSharedModuleData(string $alias): mixed
    {
        $alias = $alias."_".htcms_get_site_id();
        return (isset($this->sharedModuleData[$alias]) && !empty($this->sharedModuleData[$alias])) ? $this->sharedModuleData[$alias] : null;
    }

    /**
     * This is set when you call getModules();
     * @return int
     */
    public function getModulesCount(): int
    {
        return $this->totalModules;
    }



    /**
     * Get module info by alias
     * @param string $alias
     * @return mixed
     */
    public function getModuleInfo(string $alias='', bool $asArray=true): mixed
    {

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
    public function getSeoContent(): ?array
    {

        if($this->foundSeoModule) {
            return $this->seoModuleInfo;
        }
        return null;
    }

    /**
     * Check if login is required
     * @return bool
     */
    public function isLoginRequired(): bool
    {
        return $this->loginRequired;
    }


    /**
     * check if content is found
     * This works if module is flag as is_mandatory=1
     * and return data is zero
     * @return bool
     */
    public function isContentFound(): bool
    {
        return $this->contentFound;
    }

    /**
     * Check module mandatory
     * @param bool $checkMandatory
     */
    public static function setMandatoryCheck(bool $checkMandatory=true) {
        self::$mandatoryModuleCheck = $checkMandatory;
    }

    /**
     * Get mandatory check
     * @return bool
     */
    public static function getMandatoryCheck(): bool
    {
        return self::$mandatoryModuleCheck;
    }


    /**
     * Load module by alias
     * @param string $alias
     * @return array|string
     */
    public function loadModuleByModuleAlias(string $alias=''): array|string|null
    {
        $info = $this->getModuleInfo($alias, false);
        return $this->getModuleData($info);
    }

}
