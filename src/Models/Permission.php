<?php

namespace MarghoobSuleman\HashtagCms\Models;


class Permission extends AdminBaseModel
{
    protected $guarded = array();

    public function roles() {
       return $this->belongsToMany(Role::class);
    }
}
