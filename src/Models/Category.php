<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Support\Facades\DB;

use MarghoobSuleman\HashtagCms\Core\Scopes\SiteScope;
use MarghoobSuleman\HashtagCms\Core\Traits\Admin\Common;
use MarghoobSuleman\HashtagCms\Core\Scopes\LangScope;

class Category extends AdminBaseModel
{
    use Common;
    protected $guarded = array();

    protected $categorySiteTable = "category_site";

    protected $additionalColumns = ["site_id", "tenant_id", "theme_id", "icon", "icon_css",
        "header_content", "footer_content", "exclude_in_listing", "cache_category", "position"];

    /**
     * @override
     * boot
     */
    protected static function boot() {

        parent::boot();
        static::addGlobalScope(new SiteScope);
    }

    /**
     * With Lang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang() {
        return $this->hasOne(CategoryLang::class);
    }

    /**
     * With Langs
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs() {
        return $this->hasMany(CategoryLang::class)->withoutGlobalScopes();
    }


    /**
     * With theme
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function theme() {
      return $this->belongsTo(Theme::class, "id", "theme_id");
    }


    /**
     * With Tenant
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tenant() {

        return $this->belongsToMany(Tenant::class, "category_site")->withPivot($this->additionalColumns);
    }

    /**
     * With Site
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    //one category should only with one site
    public function site() {

      return $this->belongsToMany(Site::class)->withPivot($this->additionalColumns)->orderBy("position", "ASC");
        //return $this->belongsToMany(Site::class);

    }

    /**
     * Get parent only
     * @return mixed
     */
    public static function parentOnly() {
        return Category::with(['site', 'lang'])->where('parent_id', null)->get();
    }

    /**
     * Get parent only which has dynamic pattern
     * @return mixed
     */
    public static function parentOnlyDynamic() {
        return Category::with(['site', 'lang'])->where('link_rewrite_pattern', "!=", "")->get();
    }

    /**
     * @return array
     */
    public static function getTargetType(){
        return static::getEnumValues('category_langs', 'target');
    }

    /**
     * @return array
     */
    public static function getLinkRelationType(){

        return static::getEnumValues('category_langs', 'link_relation');

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() {
        return $this->hasOne(User::class, "id", "insert_by");
    }

    /**
     * @override
     * Find By Id
     *
     * @param int $id
     * @param string $with
     * @param int $tenant_id
     * @return array
     */
    public static function getById($id=0, $with='', $tenant_id=1) {


        //$category = Category::find($id, $with);

        $obj = ($with!='') ? static::with($with) : new static;

        $data = $obj->findOrFail($id);


        $where = array(array("site_id", $data->site_id),
            array("tenant_id", $tenant_id));

        $tenant_data = $data->tenant()->where($where)->get()->toArray();
        $data = $data->toArray();
        $data["tenant_wise"] = self::pivotToArray($tenant_data);
        $data["tenants"] = Site::with("tenant")->where("id", $data["site_id"])->get(["id"]);
        $data["tenants"] = ($data["tenants"]->count() > 0) ? $data["tenants"][0]->tenant : array();
        $data["tenant_id"] = $tenant_id;
        return $data;
    }

    /**
     * @param int $category_id
     * @param null $tenant_id
     * @param int $site_id
     * @param null $microsite_id
     * @return mixed
     */
    public static function getModules($category_id=1, $tenant_id=NULL, $site_id=1, $microsite_id=NULL) {

        $where = array("category_id"=>$category_id, "site_id"=>$site_id);
        if($tenant_id !== NULL) {
            $where["tenant_id"] = $tenant_id;
        }
        if($microsite_id !== NULL) {
            $where["microsite_id"] = $microsite_id;
        }
        //working here
        return DB::table('module_site')->select("*")->where($where)->orderBy("position", "ASC")->get();
    }


    /**
     * Get pivot data with relation of site
     * @param null $tenent_id
     * @param null $key
     * @return array|mixed|null
     */
    public function getFromSitePivot($tenent_id=NULL, $key=NULL) {

        if($tenent_id !== NULL) {
            $data = $this->site()->wherePivot("tenant_id", $tenent_id)->get()->toArray();
            $data = (sizeof($data) > 0) ? $data[0] : [];
            if($key === NULL) {

                return isset($data["pivot"]) ?  $data["pivot"] : [];

            }
            //If there is a key
            return (isset($data["pivot"]) && isset($data["pivot"][$key])) ? $data["pivot"][$key] : NULL;
        }

        //get all pivot
        $all = $this->site()->get()->toArray();
        $data = array();
        foreach ($all as $key=>$val) {
            $data[] = (isset($val["pivot"])) ? $val["pivot"] : array();
        }
        return $data;


    }

    /**
     * just for testing
     * Get category info by link_rewrite
     * @param string $link_rewrite
     * @param int $lang_id
     * @param int $tenant_id
     * @param int $site_id
     * @return mixed|null
     */
    public static function getByLinkrewrite($link_rewrite='', $lang_id=1, $tenant_id=1, $site_id=1) {

        $where = array(
            array("category_site.tenant_id", "=", $tenant_id),
            array("category_site.site_id", "=", $site_id),
            array("categories.publish_status", "=", 1),
            array("category_langs.lang_id", "=", $lang_id),
            array("categories.link_rewrite", "=", $link_rewrite)
        );


        $data = DB::table("category_site")
            ->where($where)
            ->join('categories', 'categories.id', '=', 'category_site.category_id')
            ->join('category_langs', 'category_langs.category_id', '=', 'category_site.category_id')
            ->select("category_site.*", "categories.*", "category_langs.*")
            ->get();


        if($data->count() == 0) {
            return NULL;
        }
        return $data[0];
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public static function getReadCounts($limit=10) {
        return self::orderBy("read_count", "DESC")->take($limit)->get(["read_count", "link_rewrite"]);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public static function getContentReadCounts($limit=10) {
        return Page::orderBy("read_count", "DESC")->take($limit)->get(["read_count", "link_rewrite"]);
    }
}
