<?php

namespace {{namespace}}Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use MarghoobSuleman\HashtagCms\Models\AdminBaseModel;

{{useModels}}
{{useLangScope}}

class {{model}} extends AdminBaseModel
{
    use SoftDeletes;
    protected $guarded = array();
    {{relationMethods}}

    {{langScopeBoot}}

}
