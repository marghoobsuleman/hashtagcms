<?php

if (! function_exists('htcms_get_resource')) {

    /**
     *
     * Get resource
     * @param string $resource
     * @return string
     */
    function htcms_get_resource($resource='') {
        $common = app()->Common;
        $path = $common->getResourcePath();
        $domain = env("MEIDA_URL");

        if($resource !== '') {
            return $domain."/".$path."/".$resource;
        }

        return "";
    }
}

if (! function_exists('htcms_get_header_menu')) {

    /**
     *
     * Get Header Menu
     * @param string $active
     * @return array
     */
    function htcms_get_header_menu($active='') {
        $common = app()->Common;
        return $common->getHeaderMenu($active);
    }
}

if (! function_exists('htcms_get_header_menu_html')) {

    /**
     *
     * Get Header Menu
     * @param int $maxLimit
     * @param null $css
     * @return string
     */
    function htcms_get_header_menu_html($maxLimit=-1, $css=null) {
        $common = app()->Common;
        return $common->getHeaderMenuHtml($maxLimit, $css);
    }
}

if (! function_exists('htcms_parse_string_for_view')) {

    /**
     *
     * Get Header Menu
     * @return string
     */
    function htcms_parse_string_for_view($string='') {
        $common = app()->Common;
        return $common->parseStringForView($string);
    }
}


if (! function_exists('findIndexInAssocArray')) {

    /**
     *
     * Find index in associate array
     * @param array $array
     * @param string $needleKey
     * @param string $needle
     * @return int
     */
    function findIndexInAssocArray($array=array(), $needleKey='', $needle='') {

        foreach ($array as $index=>$item) {
            if(isset($item[$needleKey]) && $item[$needleKey] == $needle) {
                return $index;
            }
        }
        return -1;
    }

}

if (! function_exists('getFormattedDate')) {

    /**
     *
     * Get Formatted Date
     * @param string $date
     * @return string
     */
    function getFormattedDate($date='') {
        return \Carbon\Carbon::createFromTimestamp(strtotime($date))->fromNow();
    }

}

if (! function_exists('sanitize')) {
    /**
     * Sanitize string
     * @param string $str
     * @return string|string[]|null
     */
    function sanitize($str = '')
    {
        $pattern = '/(script.*?(?:\/|&#47;|&#x0002F;)script)/ius';
        return (preg_replace($pattern, '', $str) ?? $str);
    }

}


if (! function_exists('htcms_get_domain_path')) {

    /**
     *
     * Get domain path
     * @param string $path
     * @return string
     */
    function htcms_get_domain_path($path='') {
        $domain = request()->getSchemeAndHttpHost();
        if($path == '') {
            return $domain;
        } else {
            $path = ($path == "/") ? "" : $path;
            return $domain."/".$path;
        }
    }

}


if (! function_exists('htcms_get_path')) {

    /**
     * Get path
     * @return string
     */
    function htcms_get_path($path) {
        $common = app()->Common;

        if($common->fullPathStyle()) {
            $lang = htcms_get_lang_info("iso_code");
            $tenant = htcms_get_tenant_info("link_rewrite");
            return htcms_get_domain_path($lang."/".$tenant."/".$path);
        }

        return htcms_get_domain_path($path);
    }


}

if (! function_exists('htcms_get_js_resource')) {

    /**
     *
     * Get get js path
     * @param string $path
     * @return string
     */
    function htcms_get_js_resource(string $path) {
        $common = app()->Common;
        $path = "%{js_path}%/$path";

        return $common->parseStringForPath($path);
    }

}

if (! function_exists('htcms_get_css_resource')) {

    /**
     *
     * Get get css path
     * @param string $path
     * @return string
     */
    function htcms_get_css_resource(string $path) {
        $common = app()->Common;
        $path = "%{css_path}%/$path";
        return $common->parseStringForPath($path);
    }

}

if (! function_exists('htcms_get_image_resource')) {

    /**
     *
     * Get get image path
     * @param string $path
     * @return string
     */
    function htcms_get_image_resource(string $path) {
        $common = app()->Common;
        $path = "%{image_path}%/$path";
        return $common->parseStringForPath($path);
    }

}

/***
 *
 * Some Information helpers
 *
 */




if (! function_exists('htcms_get_site_info')) {

    /**
     *
     * Get site info from the request
     * @param string|null $key
     * @return string|array
     */
    function htcms_get_site_info(string $key=null) {

        $common = app()->Common;
        return $common->getInfo('site', $key);

    }

}

if (! function_exists('htcms_get_site_id')) {

    /**
     *
     * Get site id from the request
     * @return int
     */
    function htcms_get_site_id() {
        return htcms_get_site_info("id");
    }

}

if (! function_exists('htcms_get_lang_info')) {

    /**
     *
     * Get lang info from the request
     * @param string|null $key
     * @return string|array
     */
    function htcms_get_lang_info(string $key=null) {

        $common = app()->Common;
        return $common->getInfo('language', $key);

    }

}

if (! function_exists('htcms_get_language_id')) {

    /**
     *
     * Get Language Id from the request
     * @return int
     */
    function htcms_get_language_id() {
        return htcms_get_lang_info("id");
    }
}


if (! function_exists('htcms_get_tenant_info')) {

    /**
     *
     * Get tenant info from the request
     * @param string|null $key
     * @return string|array
     */
    function htcms_get_tenant_info(string $key=null) {

        $common = app()->Common;
        return $common->getInfo('tenant', $key);
    }

}



if (! function_exists('htcms_get_category_info')) {

    /**
     *
     * Get category info from the request
     * @param string $key
     * @return string|array
     */
    function htcms_get_category_info(string $key=null) {
        $common = app()->Common;
        return $common->getInfo('category', $key);
    }
}


if (! function_exists('htcms_get_theme_info')) {

    /**
     *
     * Get category info from the request
     * @param string $key
     * @return string|array
     */
    function htcms_get_theme_info(string $key=null) {
        $common = app()->Common;
        return $common->getInfo('theme', $key);
    }
}

if (! function_exists('htcms_get_body_content')) {

    /**
     *
     * Get body content
     * @return string
     */
    function htcms_get_body_content() {
        $common = app()->Common;
        return $common->getFinalData("bodyContent");
    }
}

if (! function_exists('htcms_get_header_content')) {

    /**
     *
     * Get header content
     * @param bool $reverse
     * @return string
     */
    function htcms_get_header_content($reverse=false) {
        $common = app()->Common;
        $category = $common->getFinalData("category");
        $html = $common->getFinalData("html");
        return ($reverse == true) ? $category["header_content"].$html["theme"]["header_content"] : $html["theme"]["header_content"].$category["header_content"];
    }
}

if (! function_exists('htcms_get_header_content')) {

    /**
     *
     * Get header content
     * @param bool $reverse
     * @return string
     */
    function htcms_get_header_content($reverse=false) {
        $common = app()->Common;
        $category = $common->getFinalData("category");
        $html = $common->getFinalData("html");
        return ($reverse == true) ? $category["header_content"].$html["theme"]["header_content"] : $html["theme"]["header_content"].$category["header_content"];
    }
}

if (! function_exists('htcms_get_footer_content')) {

    /**
     *
     * Get footer content
     * @param bool $reverse
     * @return string
     */
    function htcms_get_footer_content($reverse=false) {
        $common = app()->Common;
        $category = $common->getFinalData("category");
        $html = $common->getFinalData("html");
        return ($reverse == true) ? $html["theme"]["footer_content"].$category["footer_content"]: $category["footer_content"].$html["theme"]["footer_content"];
    }
}

if (! function_exists('htcms_get_header_title')) {

    /**
     *
     * Get header title
     * @param bool $reverse
     * @return string
     */
    function htcms_get_header_title() {
        $common = app()->Common;
        $html = $common->getFinalData("html");
        return $html["header"]["title"];
    }
}

if (! function_exists('htcms_get_all_meta_tags')) {

    /**
     *
     * Get all meta as tags
     * @param bool $reverse
     * @return string
     */
    function htcms_get_all_meta_tags() {
        $common = app()->Common;
        $html = $common->getFinalData("html");
        $meta = $html["header"]["meta"];
        $mh = array();
        foreach ($meta as $metaName=>$metaValue) {
            $mh[] = "<meta name='$metaName' content='$metaValue'>";
        }
        return join("", $mh);
    }
}
if (! function_exists('htcms_get_all_link_tags')) {

    /**
     *
     * Get all link as tags
     * @param bool $reverse
     * @return string
     */
    function htcms_get_all_link_tags() {
        $common = app()->Common;
        $html = $common->getFinalData("html");
        $mh = array();
        foreach ($html["header"]["link"] as $link=>$linkValue) {
            if(isset($linkValue["href"]) && $linkValue["href"] != null) {
                $mh[] =  "<link rel='$linkValue[rel]' href='$linkValue[href]'>";
            }
        }
        return join("", $mh);
    }
}

if (! function_exists('htcms_get_shared_data')) {

    /**
     *
     * Get shared module data
     * @param string $module_alias
     * @return string
     */
    function htcms_get_shared_data($module_alias='') {
        $common = app()->Common;
        return $common->getSharedModuleData($module_alias);
    }
}
