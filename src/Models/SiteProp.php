<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use MarghoobSuleman\HashtagCms\Core\Scopes\SiteScope;


class SiteProp extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = array();

    /**
     * @override
     * boot
     */
    protected static function boot() {

        parent::boot();
        static::addGlobalScope(new SiteScope);
    }

    /**
     * Get Site Group
     * @return array
     */
    public static function getSiteGroup() {
        $siteGroup = SiteProp::all("group_name")->where("group_name", "!=", "")->groupBy("group_name")->toArray();
        $all = array();
        foreach ($siteGroup as $key=>$menu) {
            $all[] = $key;
        }
        return $all;
    }

}
