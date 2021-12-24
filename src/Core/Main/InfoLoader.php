<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;
use JetBrains\PhpStorm\ArrayShape;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Module;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\Tenant;
use MarghoobSuleman\HashtagCms\Models\Theme;
use MarghoobSuleman\HashtagCms\Models\Hook;

class InfoLoader
{

    private Site $siteInfo;
    private Lang $langInfo;
    private Tenant $tenantInfo;
    private Category $categoryInfo;
    private array $infoKeeper;
    private array $contextVars;
    private array $fullInfoKeeper;

    private array $layoutKeeper;
    protected CacheManager $cacheManager;



    function __construct()
    {
        $this->cacheManager = app()->HashtagCms->cacheManager();
    }

    /**
     * Get Site Info by domain and context
     * @param string $context
     * @param string $domain
     * @param string $fullDomain
     * @return Site|null
     */
    public function geSiteInfoByContextAndDomain(string $context='', string $domain='', string $fullDomain=''): ?Site
    {
        $siteInfo =  Site::where("context", '=', $context)
                ->orWhere('domain', '=', $domain)
                ->orWhere('domain', '=', $fullDomain)
                ->with(['tenant', 'language'])->first();

        return $siteInfo !=null ? $siteInfo : null;
    }

    /**
     * Get Site by context
     * @param string $context
     * @param int $lang_id
     * @return mixed
     */
    public function getSiteInfo(string $context, int $lang_id): array
    {
        $htCmsCommon = app()->HashtagCms;

        $query = "select s.id, s.name, sl.title, s.category_id as default_category_id, s.tenant_id as default_tenant_id, s.lang_id as default_lang_id, s.country_id as default_country_id,
                    s.under_maintenance, s.domain, s.context, s.favicon, s.lang_count                   
                    from sites s                    
                    left join site_langs sl on (sl.site_id = s.id)
                    where s.context = :context and sl.lang_id=:lang_id                    
                    and s.deleted_at is null";

        return $htCmsCommon->dbSelectOne($query, array("context"=>$context, "lang_id"=>$lang_id));

    }

    /**
     * Get lang info
     * @param string $lang_code
     * @param int|null $lang_id
     * @return Lang|null
     */
    public function getLangInfo(string $lang_code, int $lang_id=null): ?Lang
    {

        if($lang_code !== '/' && !empty($lang_code)) {
            $langInfo = Lang::where("iso_code", '=', $lang_code)->first();

            if($langInfo !== null) {
                $this->setLangInfo($langInfo);
                return $langInfo;
            }
        }

        if($lang_id!=null) {
            $langInfo = Lang::where("id", '=', $lang_id)->first();
            if($langInfo !==null) {
                $this->setLangInfo($langInfo);
                return $langInfo;
            }
        }
        return null;
    }

    /**
     * Set lang info
     * @param Lang $langInfo
     */
    public function setLangInfo(Lang $langInfo): void
    {
        $this->langInfo = $langInfo;
    }

    /**
     * Get tenant Info
     * @param string $tenant_code
     * @param int|null $tenant_id
     * @return Tenant|null
     */
    public function getTenantInfo(string $tenant_code, int $tenant_id=null): ?Tenant
    {
        if($tenant_code !== '/') {
            $tenantInfo = Tenant::where("link_rewrite", '=', $tenant_code)->first();
            if($tenantInfo != null) {
                $this->setTenantInfo($tenantInfo);
                return $tenantInfo;
            }
        }

        if($tenant_id !== null) {
            $tenantInfo = Tenant::where("id", '=', $tenant_id)->first();
            if($tenantInfo != null) {
                $this->setTenantInfo($tenantInfo);
                return $tenantInfo;
            }
        }
        return null;
    }

    /**
     * Set tenant info
     * @param Tenant $tenantInfo
     */
    public function setTenantInfo(Tenant $tenantInfo): void
    {
        $this->tenantInfo = $tenantInfo;
    }


    /**
     * Set Info Keeper
     * @param $key
     * @param $value
     */
    public function setInfoKeeper($key, $value) {
        $this->cacheManager->setInfoKeeper($key, $value);
    }

    /**
     * Get Info Keeper
     * @param null $key
     * @return mixed
     */
    public function getInfoKeeper($key=null): mixed
    {
        return $this->cacheManager->getInfoKeeper($key);
    }

    /**
     * Has in infoKeeper
     * @param $key
     * @return mixed
     */
    public function hasInInfoKeeper($key): mixed
    {
        return $this->infoKeeper[$key] ?? null;
    }

    /**
     * Get context vars
     * @param string $key
     * @return mixed
     */
    public function getContextVars(string $key): mixed
    {
        return $this->contextVars[$key] ?? null;
    }

    /**
     * This is used to replace value in db query
     * @param string $key
     * @param $value
     */
    public function setContextVars(string $key, mixed $value): void
    {
        $this->contextVars[":$key"] = array("key"=>$key, "value"=>$value);
    }

    /**
     * Set Language Id
     * @param int $lang_id
     * @param null $locale
     */
    public function setLanguageId(int $lang_id = 1, $locale=null):void {
        $this->setContextVars("lang_id", $lang_id);
        if($locale != null) {
            app()->setLocale($locale);
        }
    }

    /**
     * Get Language Id
     * @return int
     */
    public function getLanguageId():int {
        return $this->getContextVars("lang_id");
    }

    /**
     * @param string $key
     * @param string|null $subKey
     * @return mixed
     */
    public function getObjInfo(string $key, string $subKey=null): mixed
    {
        if($subKey == null) {
            return $this->fullInfoKeeper[$key] ?? null;
        }

        return $this->fullInfoKeeper[$key][$subKey] ?? null;
    }

    /**
     * @param mixed $key
     * @param mixed|null $value
     */
    public function setObjInfo(mixed $key, mixed $value=null): void
    {
        if(is_array($key)) {
            foreach ($key as $k=>$v) {
                $this->fullInfoKeeper[$k] = $v;
            }
        } else {
            $this->fullInfoKeeper[$key] = $value;
        }
    }

    /**
     * Set context variable to replace in query etc
     * @param int $category_id
     * @param int $site_id
     * @param int $tenant_id
     * @param int $microsite_id
     */
    public function setMultiContextVars(int $category_id, int $site_id, int $tenant_id, int $microsite_id=0):void {
        $this->setContextVars("category_id", $category_id);
        $this->setContextVars("site_id", $site_id);
        $this->setContextVars("tenant_id", $tenant_id);
        $this->setContextVars("microsite_id", $microsite_id);
    }

    /**
     * Get category info based on link_rewrite
     * Example: test or test/checking/me
     * @param string $category_link_rewrite
     * @param string $full_category_link_rewrite
     * @param int $site_id
     * @return Category|null
     */
    public function getCategoryInfo(string $category_link_rewrite, string $full_category_link_rewrite, int $site_id):?Category
    {

        $cat1 = null;
        $cat2 = null;

        // eg 1: support
        if(!empty($category_link_rewrite)) {
            $cat1 = Category::where(array(
                array("link_rewrite", '=', $category_link_rewrite),
                array("site_id", '=', $site_id)
                ))->first();
        }
        // eg 2. en/web/support/xyz-link/and-more-link
        if(!empty($full_category_link_rewrite)) {
            $cat2 = Category::where(array(
                array("link_rewrite", '=', $full_category_link_rewrite),
                array("site_id", '=', $site_id)
            ))->first();
        }

        $categoryInfo = ($cat2 !== null) ? $cat2 : $cat1; //priority give to full path url (eg 2)

        if($categoryInfo !== null) {
            $this->setCategoryInfo($categoryInfo);
        }

        return $categoryInfo !== null ? $categoryInfo : null;
    }

    /**
     * Set category Info
     * @param Category $category
     * @return void
     */
    public function setCategoryInfo(Category $category)
    {
        $this->categoryInfo =  $category;
    }


    /**
     * Get lang site info
     * @param string $lang_code
     * @param int $site_id
     * @return array|null
     */
    public function getLangSiteInfo(string $lang_code, int $site_id): ?array
    {
        $htCmsCommon = app()->HashtagCms;

        $query = "select lang_site.*, langs.* from lang_site left join langs on(lang_site.lang_id = langs.id) 
                    where site_id=:site_id and langs.iso_code=:lang_code and langs.deleted_at is null;";

        $info = $htCmsCommon->dbSelectOne($query, array("lang_code"=>$lang_code, "site_id"=>$site_id));

        return sizeof($info) > 0 ? $info : null;
    }

    /**
     * @param string $tenant_link_rewrite
     * @param int $site_id
     * @return array|null
     */
    public function getTenantSiteInfo(string $tenant_link_rewrite, int $site_id): ?array
    {
        $htCmsCommon = app()->HashtagCms;

        $query = "select site_tenant.*, tenants.*
                    from site_tenant
                    left join tenants on(site_tenant.tenant_id = tenants.id)
                    where site_tenant.site_id=:site_id and tenants.link_rewrite=:link_rewrite and tenants.deleted_at is null;";

        $info = $htCmsCommon->dbSelectOne($query, array("link_rewrite"=>$tenant_link_rewrite, "site_id"=>$site_id));

        return sizeof($info) > 0 ? $info : null;
    }

    /**
     * @param string $category_link_rewrite
     * @param int $site_id
     * @param int $tenant_id
     * @param int $lang_id
     * @param string|null $fullUrl
     * @return array|null
     */
    public function getCategorySiteInfo(string $category_link_rewrite, int $site_id, int $tenant_id, int $lang_id, string $fullUrl=null): ?array
    {
        $htCmsCommon = app()->HashtagCms;

        $query = "select c.id, c.controller_name, c.required_login, c.parent_id, c.site_id, c.is_site_default, c.is_root_category, c.is_new, c.has_wap,
        c.wap_url, c.link_rewrite, c.link_navigation, c.link_rewrite_pattern, c.has_some_special_module,c.read_count,
        cs.site_id, cs.tenant_id, cs.theme_id, cs.icon_css, cs.header_content, cs.footer_content, cs.cache_category,
        cl.name, cl.title, cl.excerpt, cl.content, cl.active_key,
        cl.third_party_mapping_key, cl.b2b_mapping, cl.is_external, cl.link_relation, cl.target,
        cl.meta_title, cl.meta_keywords, cl.meta_description, cl.meta_robots, cl.meta_canonical, c.created_at, c.updated_at
        from categories c
        left join category_site cs on(cs.category_id = c.id)
        left join category_langs cl on(c.id = cl.category_id)
        where cs.tenant_id=:tenant_id and cs.site_id=:site_id and c.publish_status=1 and c.deleted_at is null and cl.is_external=0 and cl.lang_id=:lang_id and ";

        $hasFullUrl = !empty($fullUrl);

        if($hasFullUrl) {
            $query .= " (";
        }

        $query .= " c.link_rewrite =:link_rewrite ";

        if($hasFullUrl) {
            $query .= " OR c.link_rewrite = '$fullUrl' )";
        }

        $info = $htCmsCommon->dbSelect($query, array("link_rewrite"=>$category_link_rewrite, "site_id"=>$site_id,
                                                    "tenant_id"=>$tenant_id, "lang_id"=>$lang_id));

        // if there is two results find from the full url
        // ie: you pass blog in `$category_link_rewrite` and `$fullUrl` blog/any-content-link, it will return blog/any-content-link

        if($info !== null & sizeof($info) > 1) {
            for($i=0;$i<count($info);$i++) {
                if($fullUrl === $info[$i]->link_rewrite) {
                    return (array)$info[$i];
                }
            }
        }

        return sizeof($info) > 0 ? (array)$info[0] : null;
    }

    /**
     * Get theme info
     * @param int $theme_id
     * @return Theme
     */
    public function getThemeInfo(int $theme_id):Theme
    {
        return Theme::find($theme_id);
    }


    /**
     * Get hook info based on site
     * @param string $alias
     * @param int $site_id
     * @return array|null
     */
    public function getHookInfo(string $alias, int $site_id): ?array
    {
        $htCmsCommon = app()->HashtagCms;

        $query = "select hook_site.*, hooks.* 
                    from hook_site
                    left join hooks on (hooks.id = hook_site.hook_id)
                    where hooks.alias=:alias and hook_site.site_id=:site_id;";

        $info = $htCmsCommon->dbSelectOne($query, array("alias"=>$alias, "site_id"=>$site_id));

        return sizeof($info) > 0 ? $info : null;
    }


    /**
     * Get module info by alias
     * @param string $alias
     * @param int $site_id
     * @return stdClass|null
     */
    public function getModuleInfo(string $alias, int $site_id):?\stdClass {
        $htCmsCommon = app()->HashtagCms;
        $query = "select * from modules where alias=:alias and site_id=:site_id";
        $info = $htCmsCommon->dbSelect($query, array("alias"=>$alias, "site_id"=>$site_id));

        return sizeof($info) > 0 ? $info[0] : null;


    }


    /**
     * Get all modules based on hook and category etc
     * @param int $category_id
     * @param int $tenant_id
     * @param int $site_id
     * @param int|null $microsite_id
     * @param int|null $hook_id
     * @return array|null
     */
    public function getModuleSiteInfo(int $category_id, int $tenant_id, int $site_id, int $microsite_id=null, int $hook_id=null): ?array
    {
        $htCmsCommon = app()->HashtagCms;
        $params = array("category_id"=>$category_id, "tenant_id"=>$tenant_id, "site_id"=>$site_id, "microsite_id"=>$microsite_id);
        $withHook = "";
        if($hook_id != null) {
            $withHook = " AND ms.hook_id=:hook_id";
            $params['hook_id'] = $hook_id;
        }

        $query = "select ms.module_id, ms.hook_id, ms.position,
        m.name, m.alias, m.is_seo_module, m.linked_module, m.view_name, m.data_type, m.query_statement, m.shared,
        m.query_as, m.data_handler, m.data_key_map, m.is_mandatory,
        m.method_type, m.service_params, m.individual_cache, m.cache_group
        from module_site ms
        left join modules m on (ms.module_id = m.id)
        where ms.category_id = :category_id AND ms.site_id = :site_id
        AND ms.tenant_id = :tenant_id AND ms.microsite_id = :microsite_id $withHook
        order by ms.hook_id, ms.position ASC";

        return $htCmsCommon->dbSelect($query, $params);

    }

    /**
     * Get Module site info by hooks array
     * Need to set context variables before this
     * @param array $parsedTheme
     * @param int $category_id
     * @param int $site_id
     * @param int $tenant_id
     * @param int $microsite_id
     * @return array
     */
    #[ArrayShape(["hooks" => "array|mixed", "modules" => "mixed"])] public function getModuleSiteInfoDataByParsedHooksAndModules(array $parsedTheme, int $category_id, int $site_id, int $tenant_id, int $microsite_id):array
    {

        $parsedHooks = $parsedTheme['hooks'];
        $parsedModules = $parsedTheme['modules'];

        //$moduleDataLoader = app()->HashtagCmsModuleLoader;
        //$env = $environment = strtolower(env("APP_ENV"));
        foreach ($parsedHooks as $index=>$currentHook) {

            unset($parsedHooks[$index]['created_at']);
            unset($parsedHooks[$index]['updated_at']);
            unset($parsedHooks[$index]['deleted_at']);

            $hoodPlaceHolder = "%{cms.hook.".$currentHook['alias']."}%";
            $parsedHooks[$index]['placeholder'] = $hoodPlaceHolder;

            $parsedHooks[$index]['modules'] = $this->getModuleSiteInfo($category_id, $tenant_id, $site_id, $microsite_id, $currentHook['id']);
            //get data for each module
            foreach ($parsedHooks[$index]['modules'] as $moduleIndex=>$currentModule) {
                $this->getAndManipulateModuleData($currentModule);
            }
        }


        //for modules
        if(sizeof($parsedModules) > 0) {
            foreach ($parsedModules as $index=>$currentModule) {
                $this->getAndManipulateModuleData($currentModule);
            }
        }

        return array("hooks"=>$parsedHooks, "modules"=>$parsedModules);
    }

    /**
     * @param \stdClass $module
     * @return void
     */
    private function getAndManipulateModuleData(\stdClass $module): void
    {
        $env = $environment = strtolower(env("APP_ENV"));
        $moduleLoader = app()->HashtagCms->moduleLoader();
        $module->placeholder = "%{cms.module.".$module->alias."}%";
        $module->data = $moduleLoader->getModuleData($module);
        if($env === 'prod' && $module->individual_cache === 0) {
            unset($module->data_handler);
            unset($module->data_key_map);
            unset($module->query_statement);
        }
        $this->unsetDates($module);
    }

    /**
     * @param \stdClass $obj
     * @return void
     */
    private function unsetDates(\stdClass $obj): void
    {
        unset($obj->created_at);
        unset($obj->updated_at);
        if(isset($obj->deleted_at)) {
            unset($obj->deleted_at);
        }
    }
    
    
    /**
     * Get site props
     * @param int $site_id
     * @param int $tenant_id
     * @return array
     */
    public function getSitePropsInfo(int $site_id, int $tenant_id):array {
        $htCmsCommon = app()->HashtagCms;
        $query = "select name, value, group_name from site_props where is_public=1 and site_id=:site_id and tenant_id=:tenant_id";
        $params = array("site_id"=>$site_id, "tenant_id"=>$tenant_id);
        return $htCmsCommon->dbSelect($query, $params);
    }


    /**
     * Set layout info
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setLayoutInfo(string $key, mixed $value):void
    {
        $this->layoutKeeper[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getLayoutInfo(string $key):mixed
    {
        return $this->layoutKeeper[$key] ?? null;
    }

    /******************************************************** Mostly used in API *********************************************************/

    /**
     * Get all supported Tenants
     * @param int $site_id
     * @return array
     */
    public function getAllSupportedTenants(int $site_id):array
    {
        $htCmsCommon = app()->HashtagCms;
        $query = "select site_tenant.site_id, tenants.* from site_tenant 
                    left join tenants on (site_tenant.tenant_id=tenants.id)
                  where site_tenant.site_id=:site_id and tenants.deleted_at is null;";
        $params = array("site_id"=>$site_id);
        return $htCmsCommon->dbSelect($query, $params);
    }

    /**
     * Get all supported Langs
     * @param int $site_id
     * @return array
     */
    public function getAllSupportedLangs(int $site_id):array
    {
        $htCmsCommon = app()->HashtagCms;
        $query = "select langs.*, lang_site.position from lang_site
                    left join langs on (lang_site.lang_id=langs.id)
                    where lang_site.site_id=:site_id and langs.deleted_at is null;";
        $params = array("site_id"=>$site_id);
        return $htCmsCommon->dbSelect($query, $params);
    }


    /**
     * Get all supported currencies
     * @param int $site_id
     * @return array
     */
    public function getAllSupportedCurrencies(int $site_id):array
    {
        $htCmsCommon = app()->HashtagCms;
        $query = "select currencies.*, currency_site.* from currency_site
                    left join currencies on (currency_site.currency_id=currencies.id)
                    where currency_site.site_id=:site_id and currencies.deleted_at is null;";
        $params = array("site_id"=>$site_id);
        return $htCmsCommon->dbSelect($query, $params);
    }

    /**
     * Get all supported zones
     * @param int $site_id
     * @return array
     */
    public function getAllSupportedZones(int $site_id):array
    {
        $htCmsCommon = app()->HashtagCms;
        $query = "select zones.id,zones.name from site_zone
                left join zones on (site_zone.zone_id=zones.id)
                where site_zone.site_id=:site_id and zones.deleted_at is null;";
        $params = array("site_id"=>$site_id);
        return $htCmsCommon->dbSelect($query, $params);
    }

    /**
     * Get all supported countries
     * @param int $site_id
     * @param int $lang_id
     * @return array
     */
    public function getAllSupportedCountries(int $site_id, int $lang_id):array
    {
        $htCmsCommon = app()->HashtagCms;
        $query = "select countries.id, country_langs.name, countries.zone_id, countries.currency_id, countries.iso_code, countries.call_prefix, 
                    countries.contains_states, countries.need_identification_number, countries.need_zip_code, countries.zip_code_format, 
                    countries.display_tax_label, country_langs.name
                    from country_site
                    left join countries on (country_site.country_id=countries.id)
                    left join country_langs on(countries.id = country_langs.country_id)
                    where country_site.site_id=:site_id and country_langs.lang_id=:lang_id and countries.deleted_at is null;";
        $params = array("site_id"=>$site_id, "lang_id"=>$lang_id);
        return $htCmsCommon->dbSelect($query, $params);
    }


    /**
     * Get all supported countries
     * @param int $site_id
     * @param int $lang_id
     * @param int|null $tenant_id
     * @return array
     */
    public function getAllSupportedCategories(int $site_id, int $lang_id, int $tenant_id=null):array
    {
        $htCmsCommon = app()->HashtagCms;
        $query = "select categories.id,categories.parent_id,categories.site_id, categories.is_site_default,categories.is_root_category, categories.is_new,
                categories.has_wap,categories.wap_url,categories.link_rewrite,categories.link_navigation,categories.link_rewrite_pattern,categories.controller_name,
                categories.has_some_special_module,categories.special_module_alias,categories.required_login, categories.insert_by, categories.update_by, 
                categories.publish_status,categories.read_count, 
                category_langs.category_id,category_langs.lang_id,category_langs.name,category_langs.title,category_langs.excerpt,category_langs.content,category_langs.active_key,category_langs.third_party_mapping_key,category_langs.b2b_mapping,
                 category_langs.is_external,category_langs.link_relation,category_langs.target,category_langs.meta_title,category_langs.meta_keywords,category_langs.meta_description,
                category_langs.meta_robots,category_langs.meta_canonical
                from category_site
                left join categories on (category_site.category_id=categories.id)
                left join category_langs on(categories.id = category_langs.category_id)
                 where category_site.site_id=:site_id and category_langs.lang_id=:lang_id and categories.deleted_at is null and categories.publish_status>0";
        $params = array("site_id"=>$site_id, "lang_id"=>$lang_id);
        if(!empty($tenant_id)) {
            $query = $query." and category_site.tenant_id=:tenant_id";
            $params['tenant_id'] = $tenant_id;
        }
        return $htCmsCommon->dbSelect($query, $params);
    }




}
