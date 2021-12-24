<?php
namespace MarghoobSuleman\HashtagCms\Core;

use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\CategorySite;
use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\Tenant;
use MarghoobSuleman\HashtagCms\Models\Theme;

class ServiceLoader
{
    use FeEssential;

    protected $common;
    protected $dataLoader = null;
    protected $moduleLoader = null;
    protected $allModuleEtcInfo = null;

    public function __construct()
    {
        $this->common = app()->HashtagCms;

        if($this->dataLoader == null) {
            $this->dataLoader = new DataLoader();
        }

        if($this->moduleLoader == null) {
            $this->moduleLoader = new ModuleLoader();
        }


    }

    /**
     * @param string $message
     * @param int $status
     * @param array $withData
     * @return array
     */
    private function getError(string $message='', int $status=404, array $withData=[]): array
    {
        $error = array("message"=>$message, "status"=>$status);
        return array_merge($error, $withData);
    }

    /**
     * Set base info
     * @param string|null $category
     * @param string $language
     * @param string $tenant_link_rewrite
     * @param string|null $site_context
     * @param int $microsite_id
     * @return array
     */
    protected function setInitBaseInfo(string $category=null, string $language='en', string $tenant_link_rewrite='web',
                                       string $site_context=null, int $microsite_id=0) {

        $key = $category.$language.$tenant_link_rewrite.$site_context.$microsite_id;

        if(!isset($this->allModuleEtcInfo[$key])) {

            $siteInfo = Site::where("context", "=", $site_context)->first();

            if($siteInfo == null) {
                return $this->getError("Site is not defined");
            }
            $langInfo = Lang::where("iso_code", "=", $language)->first();

            if($langInfo == null) {
                return $this->getError("Language is not defined");
            }

            $tenantInfo = Tenant::where("link_rewrite", "=", $tenant_link_rewrite)->first();

            if($tenantInfo == null) {
                return $this->getError("Tenant is not defined");
            }

            $categoryInfo = Category::orderBy("id", "desc")->where("link_rewrite", "=", $category)->orWhere("id", "=", $siteInfo->category_id)->first();
            if($categoryInfo == null) {
                return $this->getError("Category is not defined");
            }
            $categorySiteInfo = CategorySite::where(array("category_id"=>$categoryInfo->id, "site_id"=>$siteInfo->id, "tenant_id"=>$tenantInfo->id))->first();

            if($categorySiteInfo == null) {
                return $this->getError("Theme is not defined this tenant or this tenant does not exists.");
            }

            $themeInfo = Theme::find($categorySiteInfo->theme_id)->first();

            $info = array("site"=>$siteInfo,
                "language"=>$langInfo,
                "tenant"=>$tenantInfo,
                "theme"=>$themeInfo,
                "category"=>$categoryInfo,
                'microsite'=>$microsite_id
                );

            /** set everything to render a page */
            $this->setEverything($info);

            $info["status"] = 200;

            $this->allModuleEtcInfo[$key] = $info;
        }

        return $this->allModuleEtcInfo[$key];

    }

    /**
     * Set and get initial base info
     * @param string|null $category
     * @param string $language
     * @param string $tenant_link_rewrite
     * @param string|null $site_context
     * @param int $microsite_id
     * @return array
     */
    public function getInitBaseInfo(string $category=null, string $language='en', string $tenant_link_rewrite='web',
                                    string $site_context=null, int $microsite_id=0) {

        return $this->setInitBaseInfo($category, $language, $tenant_link_rewrite, $site_context, $microsite_id);

    }

    /**
     * Load module by alias
     * @param string|null $name
     * @param bool $asJson
     * @param string|null $category
     * @param string $language
     * @param string $tenant_link_rewrite
     * @param string|null $site_context
     * @param int $microsite_id
     * @return array|string
     */
    public function loadModuleByAlias(string $name=null, bool $asJson=true, string $category=null, string $language='en', string $tenant_link_rewrite='web',
                                      string $site_context=null, int $microsite_id=0) {


        $info = $this->setInitBaseInfo($category, $language, $tenant_link_rewrite, $site_context, $microsite_id);

        if($info['status'] != 200) {
            return $info;
        }

        $moduleData["data"] = $this->moduleLoader->loadModuleByModuleAlias($name);

        if($asJson == false) {
            $moduleData = $this->loadHtmlView($name, $moduleData["data"]);
        } else {
            $moduleData["html"] = $this->loadHtmlView($name, $moduleData["data"]);
        }

        return $moduleData;

    }

    /**
     * Get HTML view
     * dependency to initialize baseInfo first
     * @param $module_name
     * @param $module_data
     * @return string
     */
    private function loadHtmlView($module_name, $module_data) {
        $moduleInfo = $this->moduleLoader->getModuleInfo($module_name);
        $moduleInfo["data"] = $module_data;
        return $this->getParsedViewData($moduleInfo);
    }


    /**
     * Load all modules by hook alias
     * @param string|null $name
     * @param bool $asJson
     * @param string|null $category
     * @param string $language
     * @param string $tenant_link_rewrite
     * @param string|null $site_context
     * @param int $microsite_id
     * @return array|string
     */
    public function loadModulesByHookAlias(string $name=null, bool $asJson=true, string $category=null, string $language='en', string $tenant_link_rewrite='web',
                                           string $site_context=null, int $microsite_id=0): array|string
    {


        $info = $this->setInitBaseInfo($category, $language, $tenant_link_rewrite, $site_context, $microsite_id);

        if($info['status'] != 200) {
            return $info;
        }

        $hookInfo = $this->moduleLoader->getHookInfo($name);

        $category_id = $info["category"]->id;
        $site_id = $info["site"]->id;
        $tenant_id = $info["tenant"]->id;
        $hook_id = $hookInfo["id"];

        $hookData =  $this->moduleLoader->getCategoryModules($category_id, $site_id, $tenant_id, $microsite_id, $hook_id);
        $data = [];

        foreach ($hookData as $module) {
            $data[] = $this->loadModuleByAlias($module->alias, $asJson, $category, $language, $tenant_link_rewrite, $site_context, $microsite_id);
        }

        return ($asJson) ? $data : join("", $data);
    }

}

