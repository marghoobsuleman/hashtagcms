<?php


namespace MarghoobSuleman\HashtagCms\Core\Main;

use MarghoobSuleman\HashtagCms\Models\Module;
use MarghoobSuleman\HashtagCms\Models\ModuleProp;

/**
 * Class ModuleLoader
 * @package MarghoobSuleman\HashtagCms\Core
 * @version 1.5
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
        //$this->infoLoader->setContextVars($key, $lr);
    }

    /**
     * Get query module
     * @param Module $module
     * @return array
     */
    public function getQueryModule(mixed $module): array
    {
        $query = $module->data_handler;
        $database = $module->description;
        $ml = new QueryModuleLoader($query, $database);
        return $ml->getResult();
    }

    /**
     * Get header json
     * @param mixed $module
     * @return array|mixed
     */
    private function getHeaderJson(mixed $module) {
        $moduleHeaderJson = $module->headers;
        $headerJson = [];
        if (!empty($moduleHeaderJson)) {
            try {
                $headerJson = json_decode($moduleHeaderJson, true);
                if(!empty($headerJson)) {
                    foreach ($headerJson as $key=>$value) {
                        $headerJson[$key] = ($value === "") ? request()->headers->get($key) : $value;
                    }
                } else {
                    info("Unable to parse header json for the module name: ".$module->name. ", alias: ".$module->alias);
                    $headerJson = [];
                }
            } catch (\Exception $exception) {
                info("Invalid header json for the module name: ".$module->name.", alias: ".$module->alias);
                $headerJson = [];
            }
        }
        return $headerJson;
    }

    /**
     * Get service module
     * @param mixed $module
     * @param array $withData
     * @return mixed
     */
    public function getServiceModule(mixed $module, array $withData=array()): mixed
    {

        $serviceUrl = ($module->data_handler == "" || $module->data_handler == null) ? "" : $module->data_handler;
        $methodType = $module->method_type;
        $headerJson = $this->getHeaderJson($module);

        $service_params = $module->service_params; //make data
        if($service_params) {
            parse_str($service_params, $arguments);
            $withData = array_merge($withData, $arguments);
        }

        $ml = new ServiceModuleLoader($serviceUrl, $methodType, $withData, $headerJson);
        return $ml->getResult();
    }

    /**
     * Get module from a service URL. It also parse all query params and send it the service url
     * @param mixed $module
     * @param array $withData
     * @return array|null
     */
    public function getUrlServiceModule(mixed $module, array $withData=array()): ?array
    {

        $serviceUrl = ($module->data_handler == "" || $module->data_handler==null) ? "" : $module->data_handler;
        $methodType = $module->method_type;
        $dataKeyMap = $module->data_key_map;
        $headerJson = $this->getHeaderJson($module);

        $service_params = $module->service_params; //make data
        if($service_params) {
            parse_str($service_params, $arguments);
            $withData = array_merge($withData, $arguments);
        }

        $ml = new UrlServiceModuleLoader($serviceUrl, $methodType, $withData, $dataKeyMap, $headerJson);
        return $ml->getResult();
    }

    /**
     * Get query service module
     * if query as data: call service, execute query and return datas
     * else if query is as param: execute query, replace query with servie url param/value and call service and return data
     * @param mixed $module
     * @return array|null
     */
    public function getQueryServiceModule(mixed $module): ?array
    {
        $query = $module->query_statement;
        $serviceUrl = ($module->data_handler == "" || $module->data_handler==null) ? "" : $module->data_handler;
        $methodType = $module->method_type;
        $dataKeyMap = $module->data_key_map;
        $database = $module->description;

        $dataHandler = $module->data_handler;
        $queryAs = $module->query_as;

        $ml = new QueryServiceModuleLoader($query, $serviceUrl, $queryAs, $methodType);
        return $ml->getResult();
    }

    /**
     * Get Static Module
     * Fetch data from a content table
     * @param mixed $module
     * @return array|null
     */
    public function getStaticModule(mixed $module):?array {
        return $this->getStaticModuleByAlias($module->data_handler);
    }

    /**
     * Get Static Module
     * Fetch data from a content table
     * @param string $alias
     * @return array|null
     */
    public function getStaticModuleByAlias(string $alias):?array {
        $ml = new StaticModuleLoader($alias);
        return $ml->getResult();
    }

    /**
     * get custom module
     * @param mixed $module
     * @return array|null
     */
    public function getCustomModule(mixed $module):?array {
        return [];
    }

    /**
     * Handle unknown module
     * Need a class under app\Parser\ModuleParser
     * Need a method whatever module type is. for example if module type is MenuService,
     * you need to create a method called getMenuServiceModule(mixed $module) in ModuleParser Class
     * @param mixed $module
     * @return array|null
     */
    public function getUnknownModule(mixed $module):?array {
        $dataType = $module->data_type;
        $moduleType =  "get{$dataType}Module";
        $appNamespace = app()->getNamespace();
        $callableApp = $appNamespace."Parser\ModuleParser";
        if(class_exists($callableApp) && method_exists($callableApp, $moduleType)) {
            $moduleParser = new $callableApp;
            return $moduleParser->{$moduleType}($module);
        }
        return [];
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

        $moduleType =  "get{$dataType}Module";
        //info("dataHandler: ".$dataHandler);
        $data = match (strtolower($dataType)) {
            "query", "service", "urlservice", "queryservice", "static", "custom" => $this->{$moduleType}($module_obj),
            default => $this->getUnknownModule($module_obj),
        };

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
        // lets check it -- && self::getMandatoryCheck()
        if(($is_mandatory == 1 && is_array($data) && sizeof($data)===0)) {
            //dd("Mandatory content is missing");
            $this->contentFound = false;
        }

        //increase module count
        $this->totalModules++;

        //Check if this is sharable
        if($is_shared == 1) {
            //info("module_obj->site_id ". json_encode($module_obj));
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
        $infoLoader = app()->HashtagCms->infoLoader();
        $alias = $alias."_".$infoLoader->getContextVars("site_id");
        $this->sharedModuleData[$alias] = $data;
    }

    /**
     * Get shared module data
     * @param string $alias
     * @return mixed
     */
    public function getSharedModuleData(string $alias): mixed
    {
        $infoLoader = app()->HashtagCms->infoLoader();
        $alias = $alias."_".$infoLoader->getContextVars("site_id"); //htcms_get_site_id();
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
     * @return array|string|null
     */
    public function loadModuleByModuleAlias(string $alias=''): array|string|null
    {
        $info = $this->getModuleInfo($alias, false);
        return $this->getModuleData($info);
    }

    /**
     * Get module props
     * @param int $moduleId
     * @param int $siteId
     * @param int $platformId
     * @return array
     */
    public function getModuleProps(int $moduleId, int $siteId, int $platformId):array {
        $modulePropsData = ModuleProp::where(array(
            array('site_id', '=', $siteId),
            array('platform_id', '=', $platformId),
            array('module_id', '=', $moduleId)
        ))->get();

        $props = array();
        foreach ($modulePropsData as $key=>$prop) {
            $props[$prop->name] = $prop->lang->value;
        }
        return $props;
    }

}

