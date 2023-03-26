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



}
