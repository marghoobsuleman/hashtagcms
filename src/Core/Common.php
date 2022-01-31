<?php
namespace MarghoobSuleman\HashtagCms\Core;


use MarghoobSuleman\HashtagCms\Core\Main\CacheManager;
use MarghoobSuleman\HashtagCms\Core\Main\InfoLoader;
use MarghoobSuleman\HashtagCms\Core\Main\LayoutManager;
use MarghoobSuleman\HashtagCms\Core\Main\DataLoader;
use MarghoobSuleman\HashtagCms\Core\Main\ModuleLoader;
use MarghoobSuleman\HashtagCms\Core\Main\Results;

class Common extends Results
{

    function __construct()
    {
        info("==========- init common -===============");
    }


    /**
     * @override
     * Parse query and get the results
     * @param string $query
     * @param array $byParams
     * @return array|null (optional)
     */
    public function dbSelect(string $query, array $byParams=array(), string $database=null):?array {
        return parent::dbSelect($query, $byParams, $database);
    }

    /**
     * @override
     * Parse query and get the results
     * @param string $query
     * @param array $byParams
     * @return array|null (optional)
     */
    public function dbSelectOne(string $query, array $byParams=array(), string $database=null):?array {
        return parent::dbSelectOne($query, $byParams, $database);
    }


    /**
     * Get Header menu
     * @param string|null $active
     * @return array
     */
    public function getHeaderMenu(string $active=null): array
    {
        return $this->layoutManager()->getHeaderMenu($active);
    }


    /**
     * Get header Menu HTML
     * @param int $maxLimit
     * @param array|null $css
     * @return string
     */
    public function getHeaderMenuHtml(int $maxLimit=-1, array $css=null):string {
        return $this->layoutManager()->getHeaderMenuHtml($maxLimit, $css);
    }


    /**
     * Set shared module data
     * @param string $alias
     * @param mixed $data
     * @return void
     */
    public function setSharedModuleData(string $alias, mixed $data):void {
        $this->moduleLoader()->setSharedModuleData($alias, $data);
    }

    /**
     * Get shared module data
     * @param string $alias
     * @return mixed
     */
    public function getSharedModuleData(string $alias): mixed
    {
        return $this->moduleLoader()->getSharedModuleData($alias);
    }

    /**
     * Get layout manager instance
     * @return LayoutManager
     */
    public function layoutManager():LayoutManager {
        return app()->HashtagCmsLayoutManager;
    }

    /**
     * Get info loader
     * @return InfoLoader
     */
    public function infoLoader():InfoLoader
    {
        return app()->HashtagCmsInfoLoader;
    }

    /**
     * Get data loader
     * @return DataLoader
     */
    public function dataLoader():DataLoader
    {
        return app()->HashtagCmsDataLoader;
    }

    /**
     * Get module loader
     * @return ModuleLoader
     */
    public function moduleLoader():ModuleLoader
    {
        return app()->HashtagCmsModuleLoader;
    }

    /**
     * Get cache manager
     * @return CacheManager
     */
    public function cacheManager():CacheManager
    {
        return app()->HashtagCmsCache;
    }

}
