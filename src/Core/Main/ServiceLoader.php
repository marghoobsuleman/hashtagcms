<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;

use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\Tenant;

class ServiceLoader extends DataLoader
{
    protected InfoLoader $infoLoader;
    protected CacheManager $cacheManager;
    protected LayoutManager $layoutManager;
    protected ModuleLoader $moduleLoader;

    function __construct()
    {
        parent::__construct();
        $this->moduleLoader = app()->HashtagCms->moduleLoader();
        $this->layoutManager = app()->HashtagCms->layoutManager();
        $this->infoLoader = app()->HashtagCms->infoLoader();
    }

    /**
     * Get site config
     * @param string $context
     * @param string|null $lang
     * @param string|null $tenant
     * @return array
     */
    public function allConfigs(string $context, string $lang=null, string $tenant=null): array
    {
        // fetch site info from request -> context
        // fetch lang info from request -> lang
        // fetch tenant info from request -> tenant
        // if lang is passed in request use that else fetch lang_id from site info
        // if tenant is passed in request use that else fetch tenant_id from site info
        // after that fetch all the info.

        if (empty($context)) {
            return $this->getErrorMessage("Site context is missing", 400);
        }
        $lang_id = null;
        $tenant_id = null;
        //@todo: Default currency id is missing
        $site = Site::where('context', '=', $context)->first();

        if (empty($site)) {
            return $this->getErrorMessage("Site not found", 404);
        }

        if(!empty($lang)) {
            $langInfo = Lang::where('iso_code', '=', $lang)->first();
            if(!empty($langInfo)) {
                $lang_id = $langInfo->id;
            }
        }
        if(!empty($tenant)) {
            $tenantInfo = Tenant::where('link_rewrite', '=', $tenant)->first();
            if(!empty($tenantInfo)) {
                $tenant_id = $tenantInfo->id;
            }
        }

        $site_id = $site->id;
        $lang_id = ($lang_id == null) ? $site->lang_id : $lang_id;
        $tenant_id = ($tenant_id == null) ? $site->tenant_id : $tenant_id; //will use this

        $siteInfo = $this->infoLoader->getSiteInfo($context, $lang_id);
        $tenants = $this->infoLoader->getAllSupportedTenants($site_id);
        $langs = $this->infoLoader->getAllSupportedLangs($site_id);
        $currencies = $this->infoLoader->getAllSupportedCurrencies($site_id);
        $zones = $this->infoLoader->getAllSupportedZones($site_id);
        $categories = $this->infoLoader->getAllSupportedCategories($site_id, $lang_id, $tenant_id);
        $countries = $this->infoLoader->getAllSupportedCountries($site_id, $lang_id);
        $props = $this->infoLoader->getSitePropsInfo($site_id, $tenant_id);

        $data['site'] = $siteInfo;
        $data["defaults"] = array("category_id"=>$site->category_id,
            "lang_id"=>$site->lang_id,
            "country_id"=>$site->country_id,
            "tenant_id"=>$site->tenant_id,
            "currency_id"=>$site->currency_id ?? 1);
        $data['tenants'] = $tenants;
        $data['langs'] = $langs;
        $data['categories'] = $categories;
        $data['currencies'] = $currencies;
        $data['zones'] = $zones;
        $data['counties'] = $countries;
        $data['props'] = $props;

        return array("data"=>$data, "status"=>200);;
    }

    /**
     * Load data
     * @param array|null $params
     * @return mixed
     */
    public function loadData(array $params=null): mixed
    {

        $this->moduleLoader::setMandatoryCheck(false);
        return parent::loadData($params);

    }

    /**
     * Load data
     * @param array|null $params
     * @return mixed
     */
    public function loadModule(array $params=null): mixed
    {
        if (empty($params["name"])) {
            return $this->getErrorMessage("Module alias is missing", 400);
        }

        $this->moduleLoader::setMandatoryCheck(false);
        $data = parent::loadData($params);
        if ($data['status'] != 200) {
            return $data;
        }
        $alias = $params["name"];
        $hooks = $data['meta']['theme']['hooks'];
        $moduleData = null;
        $moduleInfo = array();
        foreach ($hooks as $hook) {
            foreach ($hook['modules'] as $module) {
                if($module->alias === $alias) {
                    $moduleInfo = (array)$module;
                    $moduleData = $module->data;
                    break;
                }
            }
        }

        $this->layoutManager->setFinalObject($data);
        $this->layoutManager->setThemePath($data['meta']['theme']["directory"]);

        $moduleInfo['data'] = $moduleData;

        if ($moduleData === null) {
            return $this->getErrorMessage("Could not find the module alias", 400);
        }

        return array("meta"=>$data['meta'], "module"=>$moduleInfo, "status"=>200);

    }


    /**
     * Load data by hook alias
     * @param array|null $params
     * @return mixed
     */
    public function loadHook(array $params=null): mixed
    {
        if (empty($params["name"])) {
            return $this->getErrorMessage("Hook alias is missing", 400);
        }

        $this->moduleLoader::setMandatoryCheck(false);
        $data = parent::loadData($params);
        if ($data['status'] != 200) {
            return $data;
        }
        $alias = $params["name"];
        $hooks = $data['meta']['theme']['hooks'];
        $hookInfo = array();
        $hookData = null;
        foreach ($hooks as $hook) {
            if($hook['alias'] === $alias) {
                $hookData = $hook;
                break;
            }
        }
        $this->layoutManager->setFinalObject($data);
        $this->layoutManager->setThemePath($data['meta']['theme']["directory"]);

        return ($hookData===null) ? null : array("meta"=>$data['meta'], "hook"=>$hookData, "status"=>200);

    }


}
