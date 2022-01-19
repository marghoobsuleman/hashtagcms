<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use MarghoobSuleman\HashtagCms\Models\AdminBaseModel;

use MarghoobSuleman\HashtagCms\Core\Scopes\LangScope;


class ModulePropLang extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = array();

    protected static function boot() {
        parent::boot();
        static::addGlobalScope(new LangScope);
    }



}
