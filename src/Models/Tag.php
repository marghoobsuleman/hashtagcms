<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = array();

    /**
     * Get all images with the tag 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function gallery() {
        return $this->belongsToMany(Gallery::class);
    }

}
