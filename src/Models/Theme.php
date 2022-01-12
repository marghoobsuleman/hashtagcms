<?php

namespace MarghoobSuleman\HashtagCms\Models;

use MarghoobSuleman\HashtagCms\Core\Scopes\SiteScope;


class Theme extends AdminBaseModel
{
    protected $guarded = array();

    protected static function boot() {
        parent::boot();
        static::addGlobalScope(new SiteScope);
    }


    /**
     * Find theme of other site id via current site theme id.
     * current site theme id -> 1, find -> alias -> search with alias in target site
     * then return theme id
     * @param int $source_theme_id
     * @param int $target_site_id
     */
    public static function getThemeIdThroughSite(int $source_theme_id, int $target_site_id) {
        $themeInfo = Theme::withoutGlobalScopes()->where('id', '=', $source_theme_id)->first();

        if($themeInfo && $themeInfo->alias) {
            $themeWhere = array(array('alias', '=', $themeInfo->alias), array('site_id', '=', $target_site_id));
            $theme = Theme::withoutGlobalScopes()->where($themeWhere)->first();
            return $theme->id;
        }
        return null;
    }
}
