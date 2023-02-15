<?php

namespace MarghoobSuleman\HashtagCms\Core\Main;

use JetBrains\PhpStorm\ArrayShape;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Module;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\Platform;
use MarghoobSuleman\HashtagCms\Models\Theme;
use MarghoobSuleman\HashtagCms\Models\Hook;
use MarghoobSuleman\HashtagCms\Core\Utils\LayoutKeys;
use MarghoobSuleman\HashtagCms\Core\Utils\InfoKeys;

class InfoLoader
{

    private Site $siteInfo;
    private Lang $langInfo;
    private Platform $platformInfo;
    private Category $categoryInfo;
    private array $infoKeeper;
    private array $contextVars;
    private array $fullInfoKeeper;

    private array $layoutKeeper;
    protected SessionManager $sessionManager;


    protected $dataLoader;

    /** v2 */
    private array $infoData = array();
    private array $loadData = array();

    function __construct()
    {
        $this->sessionManager = app()->HashtagCms->sessionManager();

    }

    /**
     * Get Site Info by domain and context
     * @param string $context
     * @param string $domain
     * @param string $fullDomain
     * @return Site|null
     */
    public function geSiteInfoByContextAndDomain(string $context = '', string $domain = '', string $fullDomain = ''): ?Site
    {
        $siteInfo = Site::where("context", '=', $context)
            ->orWhere('domain', '=', $domain)
            ->orWhere('domain', '=', $fullDomain)
            ->with(['platform', 'language'])->first();

        return $siteInfo != null ? $siteInfo : null;
    }


    /**
     * Set Info Keeper
     * @param $key
     * @param $value
     */
    public function setInfoKeeper($key, $value)
    {
        $this->sessionManager->setInfoKeeper($key, $value);
    }

    /**
     * Get Info Keeper
     * @param null $key
     * @return mixed
     */
    public function getInfoKeeper($key = null): mixed
    {
        return $this->sessionManager->getInfoKeeper($key);
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
     * This is used to replace value in db query
     * @param string $key
     * @param $value
     */
    public function setContextVars(string $key, mixed $value): void
    {
        $this->contextVars[":$key"] = array("key" => $key, "value" => $value);
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
     * Set Language Id
     * @param int $lang_id
     * @param null $locale
     */
    public function setLanguageId(int $lang_id = 1, $locale = null): void
    {
        $this->setContextVars(InfoKeys::LANG_ID, $lang_id);
        if ($locale != null) {
            app()->setLocale($locale);
        }
    }

    /**
     * Get Language Id
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->getContextVars(InfoKeys::LANG_ID);
    }

    /**
     * @param string $key
     * @param string|null $subKey
     * @return mixed
     */
    public function getObjInfo(string $key, string $subKey = null): mixed
    {
        if ($subKey == null) {
            return $this->fullInfoKeeper[$key] ?? null;
        }

        return $this->fullInfoKeeper[$key][$subKey] ?? null;
    }

    /**
     * @param mixed $key
     * @param mixed|null $value
     */
    public function setObjInfo(mixed $key, mixed $value = null): void
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
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
     * @param int $platform_id
     * @param int $microsite_id
     */
    public function setMultiContextVars(int $category_id, int $site_id, int $platform_id, int $microsite_id = 0): void
    {
        $this->setContextVars(InfoKeys::CATEGORY_ID, $category_id);
        $this->setContextVars(InfoKeys::SITE_ID, $site_id);
        $this->setContextVars(InfoKeys::PLATFORM_ID, $platform_id);
        $this->setContextVars(InfoKeys::MICROSITE_ID, $microsite_id);
    }


    /**
     * Set layout info
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setLayoutInfo(string $key, mixed $value): void
    {
        $this->layoutKeeper[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getLayoutInfo(string $key): mixed
    {
        return $this->layoutKeeper[$key] ?? null;
    }

    /** v2 */

    /**
     * Set site data
     * @param array $siteData
     * @return void
     */
    public function setSiteData(array $siteData): void
    {
        $this->infoData[InfoKeys::SITE_DATA] = $siteData;
    }

    /**
     * Get site data
     * @return array
     */
    public function getSiteData(): array
    {
        return $this->infoData[InfoKeys::SITE_DATA];
    }

    /**
     * Set platform data
     * @param array $platfomData
     * @return void
     */
    public function setPlatformData(array $platfomData): void
    {
        $this->infoData[InfoKeys::PLATFORM_DATA] = $platfomData;
    }

    /**
     * Get platofrm data
     * @return array
     */
    public function getPlatformData(): array
    {
        return $this->infoData[InfoKeys::PLATFORM_DATA];
    }


    /**
     * Set lang data
     * @param array $langData
     * @return void
     */
    public function setLangData(array $langData): void
    {
        $this->setLanguageId($langData['id'], $langData['isoCode']);
        $this->infoData[InfoKeys::LANG_DATA] = $langData;
    }

    /**
     * Get lang data
     * @return array
     */
    public function getLangData(): array
    {
        return $this->infoData[InfoKeys::LANG_DATA];
    }


    /**
     * Set category data
     * @param array $categoryData
     * @return void
     */
    public function setCategoryData(array $categoryData): void
    {
        $this->infoData[InfoKeys::CATEGORY_DATA] = $categoryData;
    }

    /**
     * Get category data
     * @return array|null
     */
    public function getCategoryData(): array|null
    {
        return $this->infoData[InfoKeys::CATEGORY_DATA] ?? null;
    }

    /**
     * Set page data
     * @param array $pageData
     * @return void
     */
    public function setPageData(array $pageData): void
    {
        $this->infoData[InfoKeys::PAGE_DATA] = $pageData;
    }

    /**
     * Get page data
     * @return array|null
     */
    public function getPageData(): array|null
    {
        return $this->infoData[InfoKeys::PAGE_DATA] ?? null;
    }




    /**
     * Set theme data
     * @param array $themeData
     * @return void
     */
    public function setThemeData(array $themeData): void
    {
        $this->infoData[InfoKeys::THEME_DATA] = $themeData;
    }

    /**
     * Get theme data
     * @return array
     */
    public function getThemeData(): array
    {
        return $this->infoData[InfoKeys::THEME_DATA];
    }

    /**
     * Set site props
     * @param array $sitePropsData
     * @return void
     */
    public function setSitePropsData(array $sitePropsData): void
    {
        $this->infoData[InfoKeys::SITE_PROP_DATA] = $sitePropsData;
    }

    /**
     * Get site props
     * @return array
     */
    public function getSitePropsData(): array
    {
        return $this->infoData[InfoKeys::SITE_PROP_DATA];
    }

    /**
     * Get site props as key val
     * @return array
     */
    public function getSitePropsDataKeyVal(): array
    {
        $props = array();
        $siteProps = $this->getSitePropsData();
        foreach ($siteProps as $key=>$val) {
            $props[$val['name']] = $val['value'];
        }
        return $props;
    }

    /**
     * Working here
     * @param string $content
     * @return string
     */
    private function addDomainInCssAndJsPath(string $content) {
        $regex = '/(?:href|src)=["\']([^"\']+\.css|[^"\']+\.js)["\']/i';
        preg_match_all($regex, $content, $matches);

        $isExternal = app()->HashtagCms->useExternalApi();
        $host = request()->getHost();
        $assetPath = config("hashtagcms.info.assets_path");
        //get domain wise or defautl one
        $assetPath = (isset($assetPath[$host])) ? $assetPath[$host] : $assetPath;
        $baseUrl = $assetPath['base_url'];
        //dd($matches[1]);
        foreach ($matches[1] as $index=>$match) {
            $str = $baseUrl.$match;
            $content = str_replace($match, $str, $content);
        }
        info("============= ");
        return $content;
    }

    /**
     * Set header content
     * @param array $headerContentData
     * @return void
     */
    public function setHeaderContent(array $headerContentData): void
    {
        $content = $headerContentData[0]['html'];
        $this->infoData[InfoKeys::HEADER_CONTENT] = $this->addDomainInCssAndJsPath($content);
    }

    /**
     * Get header content
     * @return string
     */
    public function getHeaderContent(): string
    {
        return $this->infoData[InfoKeys::HEADER_CONTENT];
    }

    /**
     * Set footer content
     * @param array $footerContent
     * @return void
     */
    public function setFooterContent(array $footerContent): void
    {
        $content = $footerContent[0]['html'];
        //$content .= $content;
        //$content .= $content;
        $this->infoData[InfoKeys::FOOTER_CONTENT] = $this->addDomainInCssAndJsPath($content);
    }

    /**
     * Get footer content
     * @return string
     */
    public function getFooterContent(): string
    {
        return $this->infoData[InfoKeys::FOOTER_CONTENT];
    }

    /**
     * Set meta title
     * @param string $metaTitle
     * @return void
     */
    public function setMetaTitle(string $metaTitle): void
    {
        $this->infoData[InfoKeys::META_TITLE] = $metaTitle;
    }

    /**
     * Get meta title
     * @return string
     */
    public function getMetaTitle(): string
    {
        return $this->infoData[InfoKeys::META_TITLE];
    }

    /**
     * Set meta canonical
     * @param string $metaCanonical
     * @return void
     */
    public function setMetaCanonical(string $metaCanonical = null): void
    {
        $this->infoData[InfoKeys::META_CANONICAL] = $metaCanonical;
    }

    /**
     * Get meta canonical
     * @return string|null
     */
    public function getMetaCanonical(): string|null
    {
        return $this->infoData[InfoKeys::META_CANONICAL];
    }

    /**
     * Set meta description
     * @param string $metaDescription
     * @return void
     */
    public function setMetaDescription(string $metaDescription = null): void
    {
        $this->infoData[InfoKeys::META_DESCRIPTION] = $metaDescription;
    }

    /**
     * Get meta description
     * @return string|null
     */
    public function getMetaDescription(): string|null
    {
        return $this->infoData[InfoKeys::META_DESCRIPTION];
    }

    /**
     * Set meta keywords
     * @param string $metaKeywords
     * @return void
     */
    public function setMetaKeywords(string $metaKeywords = null): void
    {
        $this->infoData[InfoKeys::META_KEYWORDS] = $metaKeywords;
    }

    /**
     * Get meta keywords
     * @return string|null
     */
    public function getMetaKeywords(): string|null
    {
        return $this->infoData[InfoKeys::META_KEYWORDS];
    }

    /**
     * Set meta robots
     * @param string $metaRobots
     * @return void
     */
    public function setMetaRobots(string $metaRobots): void
    {
        $this->infoData[InfoKeys::META_ROBOTS] = $metaRobots;
    }

    /**
     * Get meta robots
     * @return string
     */
    public function getMetaRobots(): string|null
    {
        return $this->infoData[InfoKeys::META_ROBOTS];
    }

    /**
     * Set fav icon
     * @param string $favicon
     * @return void
     */
    public function setFavIcon(string $favicon): void
    {
        $this->infoData[InfoKeys::FAV_ICON] = $favicon;
    }

    /**
     * Get fav icon
     * @return string|null
     */
    public function getFavIcon(): string|null
    {
        return $this->infoData[InfoKeys::FAV_ICON];
    }

    /**
     * Set theme skeleton
     * @param string $skeleton
     * @return void
     */
    public function setThemeSkeleton(string $skeleton): void
    {
        $this->infoData[InfoKeys::THEME_SKELETON] = $skeleton;
    }

    /**
     * Get theme skeleton
     * @return string
     */
    public function getThemeSkeleton(): string
    {
        return $this->infoData[InfoKeys::THEME_SKELETON];
    }

    /**
     * Set everything for later use
     * @param array $loadDataObject
     * @return void
     */
    public function setLoadDataObjectAndEverything(array $loadDataObject): void
    {
        $this->loadData = $loadDataObject;

        $meta = $loadDataObject['meta'];
        $html = $loadDataObject['html'];

        $this->setObjInfo("htmlObject", $html);
        $this->setObjInfo("metaObject", $meta);

        //set everything now
        $this->setSiteData($meta['site']);
        $this->setPlatformData($meta['platform']);
        $this->setLangData($meta['lang']);
        $this->setCategoryData($meta['category']);
        $this->setPageData($meta['page']);
        $this->setThemeData($meta['theme']);
        $this->setSitePropsData($meta['props']);
        $this->setHeaderContent($html['head']['headerContent']);
        $this->setFooterContent($html['body']['footer']['footerContent']);
        $this->setMetaTitle($html['head']['title']);
        $this->setMetaCanonical($html['head']['meta']['metaCanonical']);
        $this->setMetaDescription($html['head']['meta']['metaDescription']);
        $this->setMetaKeywords($html['head']['meta']['metaKeywords']);
        $this->setMetaRobots($html['head']['meta']['metaRobots']);
        $this->setFavIcon($html['head']['links'][0]['href'] ?? "");
        $this->setThemeSkeleton($html['body']['content']['skeleton']);
    }

    /**
     * Get site context after process
     * @return string
     */
    public function getContext(): string
    {
        return $this->getInfoKeeper(LayoutKeys::CONTEXT);
    }

    /**
     * Get site id
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->getInfoKeeper(LayoutKeys::SITE_ID);
    }


    /**
     * Get lang iso code
     * @return string
     */
    public function getLangIsoCode(): string
    {
        return $this->getInfoKeeper(LayoutKeys::LANG_ISO_CODE);
    }

    /**
     * Get site id
     * @return int
     */
    public function getLangId(): int
    {
        return $this->getInfoKeeper(LayoutKeys::LANG_ID);
    }

    /**
     * Get platform linkrewrite
     * @return string
     */
    public function getPlatformLinkrewrite(): string
    {
        return $this->getInfoKeeper(LayoutKeys::PLATFORM_LINKREWRITE);
    }

    /**
     * Get category linkrewrite
     * @return string
     */
    public function getCategoryName(): string
    {
        return $this->getInfoKeeper(LayoutKeys::CATEGORY_NAME);
    }

    /**
     * get microsite name
     * @return string|null
     */
    public function getMicrositeName(): string|null
    {
        return $this->getInfoKeeper(LayoutKeys::MICROSITE) ?? null;
    }

    /**
     * Get callable for route
     * @return string
     */
    public function getAppCallable(): string
    {
        return $this->getInfoKeeper(LayoutKeys::CALLABLE_CONTROLLER) ?? "";
    }

    /**
     * Get callable values for route
     * @return mixed
     */
    public function getAppCallableValue(): mixed
    {
        return $this->getInfoKeeper(LayoutKeys::CONTROLLER_VALUE) ?? array();
    }


}
