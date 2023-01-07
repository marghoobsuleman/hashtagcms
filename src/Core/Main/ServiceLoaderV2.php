<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;

use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\Platform;
use MarghoobSuleman\HashtagCms\Models\SiteProp;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

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

class ServiceLoaderV2 extends DataLoaderV2
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
     * @param string|null $platform
     * @return array
     */
    public function allConfigs(string $context, string $lang=null, string $platform=null): array
    {
        return parent::loadConfig($context, $lang, $platform);
    }

    /**
     * Load data
     * @param array|null $params
     * @return mixed
     */
    public function loadData(string $context, string $lang=null, string $platform=null, $category=null, $microsite=null): mixed
    {
        $this->moduleLoader::setMandatoryCheck(false);
        return parent::loadData($context, $lang, $platform, $category, $microsite);

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
