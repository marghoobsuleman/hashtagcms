<?php

if (! function_exists('htcms_get_resource')) {

    /**
     *
     * Get resource
     * @param string $resource
     * @return string
     */
    function htcms_get_resource(string $resource=''): string
    {
        $layoutManager = app()->HashtagCms->layoutManager();
        $path = $layoutManager->getResourcePath();
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
    function htcms_get_header_menu(string $active=''): array
    {
        return app()->HashtagCms->getHeaderMenu($active);
    }
}

if (! function_exists('htcms_get_header_menu_html')) {

    /**
     *
     * Get Header Menu
     * @param int $maxLimit
     * @param array|null $css
     * #[ArrayShape(["item"=>array("li"=>"nav-item", "a"=>"nav-link"),
        "childItem"=>array("li"=>"", "a"=>"dropdown-item"),
        "itemWithChild"=>array("li"=>"nav-item dropdown", "a"=>"nav-link dropdown-toggle", "group"=>"dropdown-menu"),
        "active"=>"active"
    ])]
     * @return string
     */
    function htcms_get_header_menu_html(int $maxLimit=-1, array $css=null): string
    {
        return app()->HashtagCms->getHeaderMenuHtml($maxLimit, $css);
    }
}

if (! function_exists('htcms_parse_string_for_view')) {

    /**
     *
     * Get Header Menu
     * @return string
     */
    function htcms_parse_string_for_view($string=''):string
    {
        return app()->HashtagCms->layoutManager()->parseStringForView($string);
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
    function findIndexInAssocArray(array $array=array(), string $needleKey='', string $needle=''): int
    {

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
    function getFormattedDate(string $date=''):string
    {
        return \Carbon\Carbon::createFromTimestamp(strtotime($date))->fromNow();
    }

}

if (! function_exists('sanitize')) {
    /**
     * Sanitize string
     * @param string $str
     * @return string|mixed
     */
    function sanitize(string $str = ''):?string
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
    function htcms_get_domain_path(string $path=''):string
    {
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
    function htcms_get_path(string $path):string
    {
        $layoutManager = app()->HashtagCms->layoutManager();

        if($layoutManager->fullPathStyle()) {
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
    function htcms_get_js_resource(string $path):string
    {
        return app()->HashtagCms->layoutManager()->parseStringForPath("%{js_path}%/$path");
    }

}

if (! function_exists('htcms_get_css_resource')) {

    /**
     *
     * Get get css path
     * @param string $path
     * @return string
     */
    function htcms_get_css_resource(string $path):string
    {
        return app()->HashtagCms->layoutManager()->parseStringForPath("%{css_path}%/$path");
    }

}

if (! function_exists('htcms_get_image_resource')) {

    /**
     *
     * Get get image path
     * @param string $path
     * @return string
     */
    function htcms_get_image_resource(string $path):string
    {
        return app()->HashtagCms->layoutManager()->parseStringForPath("%{image_path}%/$path");
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
    function htcms_get_site_info(string $key=null): string|array|null
    {
        return app()->HashtagCms->infoLoader()->getObjInfo('site', $key);
    }

}

if (! function_exists('htcms_get_site_id')) {

    /**
     *
     * Get site id from the request
     * @return int
     */
    function htcms_get_site_id():int
    {
        return app()->HashtagCms->infoLoader()->getInfoKeeper("site_id");
    }

}

if (! function_exists('htcms_get_lang_info')) {

    /**
     *
     * Get lang info from the request
     * @param string|null $key
     * @return string|array|null
     */
    function htcms_get_lang_info(string $key=null): string|array|null
    {
        return app()->HashtagCms->infoLoader()->getObjInfo('language', $key);

    }

}

if (! function_exists('htcms_get_language_id')) {

    /**
     *
     * Get Language Id from the request
     * @return int
     */
    function htcms_get_language_id():int
    {
        return app()->HashtagCms->infoLoader()->getInfoKeeper("lang_id");
    }
}


if (! function_exists('htcms_get_tenant_info')) {

    /**
     *
     * Get tenant info from the request
     * @param string|null $key
     * @return string|array|null
     */
    function htcms_get_tenant_info(string $key=null): string|array|null
    {
        return app()->HashtagCms->infoLoader()->getObjInfo('tenant', $key);
    }

}



if (! function_exists('htcms_get_category_info')) {

    /**
     *
     * Get category info from the request
     * @param string|null $key
     * @return string|array|null
     */
    function htcms_get_category_info(string $key=null): string|array|null
    {
        $info = app()->HashtagCms->layoutManager()->getMetaObject("category");
        return $key == null ? $info : $info[$key];
    }
}


if (! function_exists('htcms_get_theme_info')) {

    /**
     *
     * Get category info from the request
     * @param string|null $key
     * @return string|array|null
     */
    function htcms_get_theme_info(string $key=null): string|array|null
    {
        return app()->HashtagCms->infoLoader()->getObjInfo('theme', $key);
    }
}

if (! function_exists('htcms_get_body_content')) {

    /**
     *
     * Get body content
     * @return string
     */
    function htcms_get_body_content():string
    {
        return app()->HashtagCms->layoutManager()->getBodyContent();
    }
}

if (! function_exists('htcms_get_header_content')) {

    /**
     *
     * Get header content
     * @param bool $reverse
     * @return string
     */
    function htcms_get_header_content(bool $reverse=false):string
    {
        $layoutManager = app()->HashtagCms->layoutManager();
        $category = $layoutManager->getMetaObject("category");
        $theme = $layoutManager->getMetaObject("theme");
        $content = ($reverse == true) ? $category["header_content"].$theme["header_content"] : $theme["header_content"].$category["header_content"];
        return app()->HashtagCms->layoutManager()->parseStringForPath($content);
    }
}

if (! function_exists('htcms_get_footer_content')) {

    /**
     *
     * Get footer content
     * @param bool $reverse
     * @return string
     */
    function htcms_get_footer_content(bool $reverse=false):string
    {
        $layoutManager = app()->HashtagCms->layoutManager();
        $category = $layoutManager->getMetaObject("category");
        $theme = $layoutManager->getMetaObject("theme");
        $content = ($reverse == true) ? $theme["footer_content"].$category["footer_content"] : $category["footer_content"].$theme["footer_content"];
        return app()->HashtagCms->layoutManager()->parseStringForPath($content);
    }
}

if (! function_exists('htcms_get_header_title')) {

    /**
     *
     * Get header title
     * @return string
     */
    function htcms_get_header_title():string
    {
        return app()->HashtagCms->layoutManager()->getTitle();
    }
}

if (! function_exists('htcms_get_all_meta_tags')) {

    /**
     *
     * Get all meta as tags
     * @return string
     */
    function htcms_get_all_meta_tags():string
    {
        return app()->HashtagCms->layoutManager()->getMetaContent();
    }
}

if (! function_exists('htcms_get_shared_data')) {

    /**
     *
     * Get shared module data
     * @param string $module_alias
     * @return mixed
     */
    function htcms_get_shared_data(string $module_alias=''):mixed
    {
        return app()->HashtagCms->getSharedModuleData($module_alias);
    }
}

if (! function_exists('htcms_get_site_props')) {

    /**
     *
     * Get site props for frontend use
     * @param bool $asJson
     * @return string|array
     */
    function htcms_get_site_props(bool $asJson=false):string|array
    {
        $categoryInfo = htcms_get_category_info();
        $siteProps = array(
            "siteId"=>htcms_get_site_id(),
            "categoryId"=>$categoryInfo['id'],
            "categoryName"=>$categoryInfo['name'],
            "categoryLinkRewrite"=>$categoryInfo['link_rewrite'],
            "tenantId"=>htcms_get_tenant_info('id'),
            "tenantName"=>htcms_get_tenant_info('name'),
            "pageId"=> $categoryInfo['page_id'] ?? -1,
            "pageLinkRewrite"=> $categoryInfo['page_link_rewrite'] ?? "",
            "pageName"=> $categoryInfo['page_name'] ?? ""
        );
        return ($asJson == true) ? json_encode($siteProps) : $siteProps;
    }
}
