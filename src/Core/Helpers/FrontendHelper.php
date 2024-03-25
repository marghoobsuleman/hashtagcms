<?php

if (! function_exists('htcms_get_resource')) {

    /**
     * Get resource
     */
    function htcms_get_resource(string $resource = ''): string
    {
        $layoutManager = app()->HashtagCms->layoutManager();
        $path = $layoutManager->getResourcePath();
        $domain = env('MEIDA_URL');

        if ($resource !== '') {
            return $domain.'/'.$path.'/'.$resource;
        }

        return '';
    }
}

if (! function_exists('htcms_get_header_menu')) {

    /**
     * Get Header Menu
     */
    function htcms_get_header_menu(string $active = ''): array
    {
        return app()->HashtagCms->getHeaderMenu($active);
    }
}

if (! function_exists('htcms_get_header_menu_html')) {

    /**
     * Get Header Menu
     *
     * @param  array|null  $css
     *                           #[ArrayShape(["item"=>array("li"=>"nav-item", "a"=>"nav-link"),
        "childItem"=>array("li"=>"", "a"=>"dropdown-item"),
        "itemWithChild"=>array("li"=>"nav-item dropdown", "a"=>"nav-link dropdown-toggle", "group"=>"dropdown-menu"),
        "active"=>"active"
    ])]
     */
    function htcms_get_header_menu_html(array $data, ?int $maxLimit = null, ?array $css = null): string
    {
        $maxLimit = ($maxLimit == null) ? -1 : $maxLimit;

        return app()->HashtagCms->getHeaderMenuHtml($data, $maxLimit, $css);
    }
}

if (! function_exists('htcms_parse_string_for_view')) {

    /**
     * Get Header Menu
     */
    function htcms_parse_string_for_view($string = ''): string
    {
        return app()->HashtagCms->layoutManager()->parseStringForView($string);
    }
}

if (! function_exists('findIndexInAssocArray')) {

    /**
     * Find index in associate array
     */
    function findIndexInAssocArray(array $array = [], string $needleKey = '', string $needle = ''): int
    {

        foreach ($array as $index => $item) {
            if (isset($item[$needleKey]) && $item[$needleKey] == $needle) {
                return $index;
            }
        }

        return -1;
    }

}

if (! function_exists('getFormattedDate')) {

    /**
     * Get Formatted Date
     */
    function getFormattedDate(?string $date = null): string
    {
        return ($date != null) ? \Carbon\Carbon::createFromTimestamp(strtotime($date))->fromNow() : '';
    }

}

if (! function_exists('sanitize')) {
    /**
     * Sanitize string
     *
     * @return string|mixed
     */
    function sanitize(string $str = ''): ?string
    {
        $pattern = '/(script.*?(?:\/|&#47;|&#x0002F;)script)/ius';

        return preg_replace($pattern, '', $str) ?? $str;
    }

}

if (! function_exists('htcms_get_domain_path')) {

    /**
     * Get domain path
     */
    function htcms_get_domain_path(string $path = ''): string
    {
        $domain = request()->getSchemeAndHttpHost();
        if ($path == '') {
            return $domain;
        } else {
            $path = ($path == '/') ? '' : $path;

            return $domain.'/'.$path;
        }
    }

}

if (! function_exists('htcms_get_path')) {

    /**
     * Get path
     */
    function htcms_get_path(string $path): string
    {
        $layoutManager = app()->HashtagCms->layoutManager();

        if ($layoutManager->fullPathStyle()) {
            $lang = htcms_get_lang_info('isoCode');
            $platform = htcms_get_platform_info('linkRewrite');

            return htcms_get_domain_path($lang.'/'.$platform.'/'.$path);
        }

        return htcms_get_domain_path($path);
    }

}

if (! function_exists('htcms_get_js_resource')) {

    /**
     * Get get js path
     */
    function htcms_get_js_resource(string $path): string
    {
        return app()->HashtagCms->layoutManager()->parseStringForPath("%{js_path}%/$path");
    }

}

if (! function_exists('htcms_get_css_resource')) {

    /**
     * Get get css path
     */
    function htcms_get_css_resource(string $path): string
    {
        return app()->HashtagCms->layoutManager()->parseStringForPath("%{css_path}%/$path");
    }

}

if (! function_exists('htcms_get_image_resource')) {

    /**
     * Get get image path
     */
    function htcms_get_image_resource(string $path): string
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
     * Get site info from the request
     */
    function htcms_get_site_info(?string $key = null): string|array|null
    {
        return htcms_data_key_or_null(app()->HashtagCms->infoLoader()->getSiteData(), $key);
    }

}

if (! function_exists('htcms_get_site_id')) {

    /**
     * Get site id from the request
     */
    function htcms_get_site_id(): int
    {
        return app()->HashtagCms->infoLoader()->getInfoKeeper('siteId');
    }

}

if (! function_exists('htcms_get_lang_info')) {

    /**
     * Get lang info from the request
     */
    function htcms_get_lang_info(?string $key = null): string|array|null
    {
        return htcms_data_key_or_null(app()->HashtagCms->infoLoader()->getLangData(), $key);
    }

}

if (! function_exists('htcms_data_key_or_null')) {

    /**
     * Get the data or data's value based on key
     */
    function htcms_data_key_or_null(array $data, ?string $key = null): mixed
    {
        if ($key == null) {
            return $data;
        }

        return $data[$key] ?? null;
    }
}

if (! function_exists('htcms_get_language_id')) {

    /**
     * Get Language Id from the request
     */
    function htcms_get_language_id(): int
    {
        return htcms_data_key_or_null(app()->HashtagCms->infoLoader()->getLangData(), 'id');
    }
}

if (! function_exists('htcms_get_platform_info')) {

    /**
     * Get platform info from the request
     */
    function htcms_get_platform_info(?string $key = null): string|array|null
    {
        return htcms_data_key_or_null(app()->HashtagCms->infoLoader()->getPlatformData(), $key);
    }

}

if (! function_exists('htcms_get_category_info')) {

    /**
     * Get category info from the request
     */
    function htcms_get_category_info(?string $key = null): string|array|null
    {
        return htcms_data_key_or_null(app()->HashtagCms->infoLoader()->getCategoryData(), $key);
    }
}

if (! function_exists('htcms_get_page_info')) {

    /**
     * Get page info from the request
     */
    function htcms_get_page_info(?string $key = null): string|array|null
    {
        return htcms_data_key_or_null(app()->HashtagCms->infoLoader()->getPageData(), $key);
    }
}

if (! function_exists('htcms_get_theme_info')) {

    /**
     * Get category info from the request
     */
    function htcms_get_theme_info(?string $key = null): string|array|null
    {
        return htcms_data_key_or_null(app()->HashtagCms->infoLoader()->getThemeData(), $key);
    }
}

if (! function_exists('htcms_get_body_content')) {

    /**
     * Get body content
     */
    function htcms_get_body_content(): string
    {
        return app()->HashtagCms->layoutManager()->getBodyContent();
    }
}

if (! function_exists('htcms_get_header_content')) {

    /**
     * Get header content
     */
    function htcms_get_header_content(bool $reverse = false): string
    {
        $layoutManager = app()->HashtagCms->layoutManager();
        $category = $layoutManager->getMetaObject('category');
        $theme = $layoutManager->getMetaObject('theme');
        $content = ($reverse == true) ? $category['header_content'].$theme['header_content'] : $theme['header_content'].$category['header_content'];

        return app()->HashtagCms->layoutManager()->parseStringForPath($content);
    }
}

if (! function_exists('htcms_get_footer_content')) {

    /**
     * Get footer content
     */
    function htcms_get_footer_content(bool $reverse = false): string
    {
        $layoutManager = app()->HashtagCms->layoutManager();
        $category = $layoutManager->getMetaObject('category');
        $theme = $layoutManager->getMetaObject('theme');
        $content = ($reverse == true) ? $theme['footer_content'].$category['footer_content'] : $category['footer_content'].$theme['footer_content'];

        return app()->HashtagCms->layoutManager()->parseStringForPath($content);
    }
}

if (! function_exists('htcms_get_header_title')) {

    /**
     * Get header title
     */
    function htcms_get_header_title(): string
    {
        return app()->HashtagCms->layoutManager()->getTitle();
    }
}

if (! function_exists('htcms_get_all_meta_tags')) {

    /**
     * Get all meta as tags
     */
    function htcms_get_all_meta_tags(): string
    {
        return app()->HashtagCms->layoutManager()->getMetaContent();
    }
}

if (! function_exists('htcms_get_shared_data')) {

    /**
     * Get shared module data
     */
    function htcms_get_shared_data(string $module_alias = ''): mixed
    {
        return app()->HashtagCms->getSharedModuleData($module_alias);
    }
}

if (! function_exists('htcms_get_site_props')) {

    /**
     * Get site props for frontend use
     */
    function htcms_get_site_props(bool $asJson = false): string|array
    {
        $categoryInfo = htcms_get_category_info();
        $platformInfo = htcms_get_platform_info();
        $pageInfo = htcms_get_page_info();
        $props = app()->HashtagCms->infoLoader()->getSitePropsDataKeyVal();
        $siteProps = [
            'siteId' => htcms_get_site_id(),
            'siteContext' => htcms_get_site_info('context'),
            'underMaintainance' => htcms_get_site_info('underMaintainance'),
            'language' => htcms_get_lang_info('name'),
            'categoryId' => $categoryInfo['id'],
            'categoryName' => $categoryInfo['name'],
            'categoryLinkRewrite' => $categoryInfo['linkRewrite'],
            'pageId' => $pageInfo['id'] ?? -1,
            'pageLinkRewrite' => $pageInfo['linkRewrite'] ?? '',
            'pageName' => $pageInfo['name'] ?? '',
            'platformId' => $platformInfo['id'],
            'platformName' => $platformInfo['name'],
            'props' => $props,
        ];

        return ($asJson == true) ? json_encode($siteProps) : $siteProps;
    }
}

if (! function_exists('____')) {

    /**
     * Four underscore
     * For lang translation by key
     *
     * @param  string  $string
     */
    function ____(string $key): string
    {
        $str = __($key); // find it in "hashtagcms::file.key"; - lang/vendor/hashtagcms/en/file

        $isHashtag = (strpos($key, 'hashtagcms::') === false) ? false : true;

        if ($isHashtag && $str === $key) {
            //could not find the lang
            //try to find in lang/en/file
            $key = str_replace('hashtagcms::', '', $key);
            $str = __($key);
        }
        //not there too. return label/key
        if ($str === $key) {
            $hasDot = (strpos($key, '.') === false) ? false : true;
            $pos = strpos($key, '.');
            $add = ($hasDot) ? 1 : 0;
            $str = substr($key, $pos + $add, strlen($key));
        }

        return $str;
    }
}
