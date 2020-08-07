<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;


class UserProfile extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = array();


    /**
     * Get genders
     * @return array
     */
    public static function genders() {
        return array("Male", "Female","Others");
    }

}
