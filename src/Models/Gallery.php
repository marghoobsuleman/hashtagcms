<?php

namespace MarghoobSuleman\HashtagCms\Models;

use MarghoobSuleman\HashtagCms\Core\Scopes\SiteScope;

class Gallery extends AdminBaseModel
{
    protected $guarded = array();


    /**
     * @override
     * boot
     */
    protected static function boot() {

        parent::boot();
        static::addGlobalScope(new SiteScope);
    }


    /**
     * Get Image Group
     * @return array
     */
    public static function getTypeGroup() {
        $siteGroup = self::all("type")->where("type", "!=", "")->groupBy("type")->toArray();
        $all = array();
        foreach ($siteGroup as $key=>$menu) {
            $all[] = $key;
        }
        return $all;
    }
    /**
     * Get Image Group
     * @return array
     */
    public static function getImageGroup() {
        $siteGroup = self::all("group")->where("group", "!=", "")->groupBy("group")->toArray();
        $all = array();
        foreach ($siteGroup as $key=>$menu) {
            $all[] = $key;
        }
        return $all;
    }

    /**
     * with tags
     * @return void
     */
    public function tag() {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Save Tags
     * @param int $sourceId
     * @param array $tags
     * @return void
     */
    public function saveTags(int $sourceId, array $tags) {
        $source = self::find($sourceId);
        $source->tag()->detach();
        foreach ($tags as $tag) {
            //Create Tag and relation
            $tag = strtolower(rtrim(ltrim($tag)));
            if ($tag != "") {
                $tagData = Tag::updateOrCreate(['name'=>$tag]);
                $source->tag()->attach($tagData);
            }
        }
    }


    /**
     * Get all category attached to image
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function category() {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get all pages attached to image
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function page() {
        return $this->belongsToMany(Page::class);
    }

}
