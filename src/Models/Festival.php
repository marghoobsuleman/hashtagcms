<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use MarghoobSuleman\HashtagCms\Core\Scopes\SiteScope;


class Festival extends AdminBaseModel
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
     * Get modules
     * @param null $user_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAllFestivals($user_id=NULL) {
        return static::orderBy("position", "asc")->get();
    }



}
