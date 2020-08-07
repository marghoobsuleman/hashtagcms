<?php

namespace MarghoobSuleman\HashtagCms\Models;


use MarghoobSuleman\HashtagCms\Core\Scopes\SiteScope;

class Tenant extends AdminBaseModel
{
    protected $guarded = array();

    /**
     * with site
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function site() {
        return $this->belongsToMany(Site::class);
    }



}
