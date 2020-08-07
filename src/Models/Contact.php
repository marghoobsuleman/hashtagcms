<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Contact extends AdminBaseModel
{
    use SoftDeletes;

    protected $guarded = array();


    /**
     * Get today's contacts
     * @return mixed
     */
    public static function today() {
        return self::whereDate('created_at', Carbon::today())->get();
    }
}
