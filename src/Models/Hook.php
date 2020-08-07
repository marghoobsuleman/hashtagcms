<?php

namespace MarghoobSuleman\HashtagCms\Models;


class Hook extends AdminBaseModel
{

    protected $guarded = array();


    public function site() {
        return $this->belongsToMany(Site::class);
    }


}
