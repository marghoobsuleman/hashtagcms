<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use MarghoobSuleman\HashtagCms\Models\AdminBaseModel;


class Media extends AdminBaseModel
{
    use SoftDeletes;

    protected $table = "medias";

    protected $guarded = array();
    

    public function lang() {
        return $this->hasMany(MediaLang::class);
    }


    

}
