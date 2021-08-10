<?php

namespace MarghoobSuleman\HashtagCms\Models;

use MarghoobSuleman\HashtagCms\Core\Scopes\SiteScope;
use Illuminate\Support\Facades\DB;


class Page extends AdminBaseModel
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
     * Get with lang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang() {
        return $this->hasOne(PageLang::class,"page_id");
    }

    /**
     * With Langs
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs() {
        return $this->hasMany(PageLang::class)->withoutGlobalScopes();
    }

    /**
     * Get content type
     * @return array
     */
    public static function getContentTypes() {
        return static::getEnumValues('pages', 'content_type');
    }

    /**
     * Get menu placement
     * @return array
     */
    public static function getMenuPlacements() {
        return array("top", "top-right", "left", "right", "bottom", "bottom-right", "other");
    }


    /**
     * Get latest blog
     * @param $siteId
     * @param $langId
     * @return array
     */
    public static function getLatestBlog($siteId, $langId) {
        return DB::select("select c.link_rewrite as category_link_rewrite, u.name as user_name, p.read_count,
p.id, p.site_id, p.microsite_id, p.tenant_id, p.category_id, p.alias, p.exclude_in_listing, p.content_type, p.position, p.link_rewrite, p.menu_placement,
p.enable_comments, p.created_at, p.updated_at, pl.name, pl.title, pl.description, pl.page_content, pl.link_relation, pl.target, pl.active_key, cmn.*
from pages p 
left join page_langs pl on (p.id = pl.page_id) 
left join categories c on(c.id=p.category_id)
left join users u on(u.id=p.insert_by)
left join (SELECT page_id as comment_page_id, COUNT(*) as comments_count FROM comments GROUP BY page_id) cmn ON cmn.comment_page_id = p.id
where p.site_id=:site_id and pl.lang_id=:lang_id and p.publish_status=1 and p.content_type='blog' and p.deleted_at is null 
order by p.created_at DESC limit 10;", array("site_id"=>$siteId, "lang_id"=>$langId));
    }

    /**
     * Get with category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category() {
        return $this->belongsTo(Category::class);
    }
}
