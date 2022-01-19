<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Site extends AdminBaseModel
{
    protected $guarded = array();

    protected $additionalColumns = ["site_id", "microsite_id", "tenant_id", "category_id",
        "hook_id", "module_id", "position", "publish_status"];

    protected $currencyColumns = ["conversion_rate", "markup", "markup_type"];

    /**
     * With lang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang() {

        return $this->hasOne(SiteLang::class);

    }

    /**
     * With Langs
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs() {
        return $this->hasMany(SiteLang::class)->withoutGlobalScopes();
    }

    /**
     * With Category
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function category() {

      return $this->hasManyThrough(CategoryLang::class, Category::class);

    }


    /**
     * Theme
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function theme() {

      return $this->hasMany(Theme::class);

    }

    /**
     * Supported Tenant
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tenant() {

        return $this->belongsToMany(Tenant::class);

    }

    /**
     * Supported Microsites
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function microsite() {
        return $this->hasMany(Microsite::class);
    }


    /**
     * Supported Language
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function language() {

      return $this->belongsToMany(Lang::class);

    }

    /**
     * Supported Country
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function country() {
        return $this->belongsToMany(Country::class);
    }

    /**
     * Supported Zone
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function zone() {
        return $this->belongsToMany(Zone::class);
    }

    /**
     * Suported hooks
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hook() {
        return $this->belongsToMany(Hook::class);
    }

    /**
     * With module
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function module() {
        return $this->belongsToMany(Module::class)->withoutGlobalScopes()->withPivot($this->additionalColumns);
    }

    /**
     * For homepage
     * @return Site[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function homepage() {
        return $this->with(['tenant', 'category', 'hook'])->withoutGlobalScopes()->get(); //'module',

    }

    public function currency() {
        return $this->belongsToMany(Currency::class)->withPivot($this->currencyColumns);
    }


    /**
     * Get Site Info with minimum fields
     * @param int $site_id
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */

    public static function allInfo($site_id=1) {
      $site = new self;
      return $site->with([
                        'category:category_id,site_id,lang_id,name,title',
                        'staticmodule:id,alias',
                        'theme:id,site_id,name,directory',
                        'tenant:id,name',
                        'language:id,name,iso_code',
                        'microsite:id,site_id,name',
                        'country:id',
                        'zone',
                        'hook',
                        'prop'
                        ])->withoutGlobalScopes()->find($site_id);
    }

    /**
     * Get Site Info with all fields
     * @param int $site_id
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */

    public static function allInfoFull($site_id=NULL) {
        $site = new self;

        if($site_id == NULL) {
            return $site->with(['category', 'theme', 'tenant', 'language', 'microsite', 'country'])->get();
        } else {
            return $site->with(['category', 'theme', 'tenant', 'language', 'microsite', 'country'])->find($site_id);
        }

    }


    /**
     * Attach country, tenant etc in site
     * @param $toAttachKey - country, etc
     * @param $toAttachIds  - array
     * @return mixed
     */
    public function attachThings($toAttachKey, $toAttachIds) {

        $key = Str::singular(strtolower($toAttachKey));

        $finder = Str::title($key);
        $finder = ($finder == "Language") ? "Lang" : $finder; //hack for language
        $namespace = config("hashtagcms.namespace");
        $finder = resolve($namespace.'Models\\'.$finder);

        $data = $finder::find($toAttachIds); //Country::find([1,2]);

        //info(json_encode($data));

        $old = $this->$key()->get(); //$this->country()->get();

        if($old->count()>0) {
            $data = $data->merge($old); //fetch old data
        }

        //remove old
        $this->$key()->detach();

        //attach now
        return $this->$key()->attach($data);
    }

    /**
     * @param $toDetachKey
     * @param array $toDetachArray
     * @return mixed
     */
    public function detachThings($toDetachKey, $toDetachArray=array()) {

        $key = Str::singular(strtolower($toDetachKey));

        if(count($toDetachArray) > 0) {
            $old = $this->$key()->whereIn("id", $toDetachArray)->get(); //$this->country()->whereIn("id", [1,2])->get();
            return $this->$key()->detach($old);
        }

        //remove old
        return $this->$key()->detach();

    }

    /**
     * Get Supported tenants
     * @param null $site_id
     * @return mixed
     */
    public static function getSupportedTenants($site_id = NULL) {
        $site_id = ($site_id === NULL) ? htcms_get_siteId_for_admin() : $site_id;
        $tenants = Site::with('tenant')->where("id", $site_id)->get();

        return $tenants[0]->tenant;

    }


    public function staticmodule() {
        return $this->hasManyThrough(StaticModuleContentLang::class, StaticModuleContent::class);
        //return $this->hasMany(StaticModuleContent::class, "site_id","id");
    }

    /**
     * Suported props
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function prop() {
        return $this->hasMany(SiteProp::class);
    }

    /**
     * Suported props
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function siteproperty() {
        return $this->prop();
    }

    public function moduleprop() {
        return $this->hasMany(ModuleProp::class);
    }

}
