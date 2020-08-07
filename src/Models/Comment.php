<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


class Comment extends AdminBaseModel
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

    /**
     * get with category
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category() {
        return $this->hasOne(Category::class, "id", "category_id");
    }

    /**
     * Get with content
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function content() {
        return $this->hasOne(Page::class, "id", "page_id");
    }


}
