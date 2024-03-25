<?php

namespace MarghoobSuleman\HashtagCms\Models;

use MarghoobSuleman\HashtagCms\Core\Scopes\LangScope;

class SiteLang extends AdminBaseModel
{
    protected $guarded = [];

    protected static function boot()
    {

        parent::boot();
        static::addGlobalScope(new LangScope);

    }
}
