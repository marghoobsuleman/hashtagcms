<?php

namespace MarghoobSuleman\HashtagCms;

use Illuminate\Support\Str;

class HashtagCms
{

    private $frontEndRouteEnabled = true;
    private $installtionRoutes = true;

    private $ignoredPath = "(?!assets/)|(?!fonts/)|(?!build/)|(?!resources/)|(?!public/)";

    /**
     * Get Header Menu as an array
     * @param string $active
     * @return array
     */
    public function getHeaderMenu($active='') {
        return htcms_get_header_menu($active);
    }

    /**
     * Get header menu as html
     * @param int $maxLimit
     * @param $css
     * @return int
     */
    public function getHeaderMenuHTML(int $maxLimit, $css) {
        return htcms_get_header_menu_html($maxLimit,  $css);
    }

    /**
     * Disabled Frontend Routes
     */
    public function disabledRoutes() {
        $this->frontEndRouteEnabled = false;
    }

    /**
     * Enable Frontend Routes
     */
    public function enableRoutes() {
        $this->frontEndRouteEnabled = true;
    }

    /**
     * Get if route is enabled
     * @return bool
     */
    public function isRoutesEnabled() {
        return $this->frontEndRouteEnabled;
    }


    /**
     * Disabled installation routes
     */
    public function disableInstallation() {
        $this->installtionRoutes = false;
    }

    /**
     * Check if installations routes are enabled
     * @return bool
     */
    public function isInstallationRoutesEnabled() {
        return $this->installtionRoutes;
    }

    /**
     * Get body content that is set by category content
     * @return mixed
     */
    public function getBodyContent() {
        return htcms_get_body_content();
    }

    /**
     * Get header content
     * @param bool $reverse
     * @return mixed
     */
    public function getHeaderContent($reverse=false) {
        return htcms_get_header_content($reverse);
    }

    /**
     * Get Footer content. it's combination of theme and category content
     * @param $reverse
     * @return string
     */
    public function getFooterContent($reverse=false) {
       return htcms_get_footer_content($reverse);
    }

    /**
     * Get header title
     * @return mixed
     */
    public function getHeaderTitle() {
        return htcms_get_header_title();
    }

    /**
     * Get all meta tags
     * @return string
     */
    public function getAllMetaTags()
    {
        return htcms_get_all_meta_tags();
    }

    /**
     * Get ignored path
     * @return string
     */
    public function getIgnoredPath():string {
        return "^(".$this->ignoredPath.".)*?";
    }

    /**
     * Set directory to ignored path
     * @param string $path
     * @return void
     */
    public function setDirectoryToIgnoredPath(string $path) {
        $this->ignoredPath = $this->ignoredPath."|(?!$path/)";
    }


}
