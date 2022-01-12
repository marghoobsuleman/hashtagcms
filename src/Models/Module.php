<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Support\Facades\DB;
use MarghoobSuleman\HashtagCms\Core\Scopes\SiteScope;

class Module extends AdminBaseModel
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
     * Get Method Types
     * @return array
     */
    public static function getMethodTypes() {
      $x = array('name'=>'GET', 'value'=>'GET');
      $y = array('name'=>'POST', 'value'=>'POST');
      $method_type=array($x,$y);
      return $method_type;
    }


    /**
     * Get Data Types
     * @return array
     */
    public static function getDataTypes() {
        $data_type = htcms_admin_config('module_types');
        //safe side
        if(empty($data_type)) {
            $data_type = array('Static','Query','Service','Custom','QueryService','UrlService');
        }
        return $data_type;
    }

    /**
     * Get Data Types Info
     * @return array
     */
    public static function getDataTypesInfo() {

        $data_type_info = array(
            "Static" => "Fetch data from CMS table (static_module_contents) ie. Content Module. <span class='text-danger'>View name is not required.</span>",
            "Query" => "Execute query from any table and database. (if database is different you need to add jdbc name in desc field.)",
            "Service" => "Fetch data from any URL. Return type will be json or text/html (if you need text/html; append 'resultType=html' in your service url)",
            "Custom" => "Don't do any special things. Just load the module.",
            "QueryService" => "It executes a query and pass those data to service URL. You can also get the data from the both.",
            "UrlService" => "You can invoke any service along with HTTP request dynamic parameters"
        );

        return $data_type_info;
    }

    /**
     * Copy Data from one category to another
     * @param $fromData
     * @param $toData
     * $param $forAllTenants
     * @return array
     */
    public static function copyData($fromData, $toData) {
        //{site_id:1, microsite_id:0, tenant_id:1, category_id:1}

        $sourceSiteId = $fromData["site_id"];
        $sourceTenantId = $fromData["tenant_id"];
        $sourceCategoryId = $fromData["category_id"];
        $sourceMicrositeId = $fromData["microsite_id"];

        $targetSiteId = $toData["site_id"];
        $targetTenantId = $toData["tenant_id"];
        $targetCategoryId = $toData["category_id"];
        $targetMicrositeId = $toData["microsite_id"];

        if(($sourceTenantId === $targetTenantId) &&
            ($sourceCategoryId === $targetCategoryId) &&
            ($sourceMicrositeId === $targetMicrositeId) &&
            ($sourceSiteId === $targetSiteId) ) {
            info("copy error");
            return array("success"=>false, "error"=>true, "message"=>"Source and target is same. Unable to copy");
        }


        //set theme category if it is not the same
        if(($sourceCategoryId !== $targetCategoryId) || ($sourceTenantId !== $targetTenantId)) {

            $fromWhere = array(array("category_site.tenant_id", "=", $sourceTenantId),
                array("category_site.site_id", "=", $sourceSiteId),
                array("category_site.category_id", "=", $sourceCategoryId)
            );
            //

            $fromCategory = DB::table('categories')
                ->join("category_site", "categories.id", "=", "category_site.category_id")
                ->where($fromWhere)->first();
            //info($fromCategory);
            if(empty($fromCategory)) {
                return array("success"=>false, "error"=>true, "message"=>"Could not find the source category. Unable to copy");
            }
            if($fromCategory->theme_id == 0 || $fromCategory->theme_id == null) {
                return array("success"=>false, "error"=>true, "message"=>"Theme is missing in source category. Unable to copy");
            }

            $theme_id = $fromCategory->theme_id;

            // if site is not the same.
            // use theme alias and get the theme id for the desired site.
            if($targetSiteId != $sourceSiteId) {
                //get old theme and fetch alias. get that alias and target site id for theme id
                $theme_id = Theme::getThemeIdThroughSite($theme_id, $targetSiteId);
            }

            $toWhere = array(array("tenant_id", "=", $targetTenantId),
                array("site_id", "=", $targetSiteId),
                array("category_id", "=", $targetCategoryId)
            );

            DB::table("category_site")->where($toWhere)->update(["theme_id"=>$theme_id]);

        }

        $data = DB::table("module_site")->where($fromData)->get();

        $newData = array();


        $user_id = auth()->user()->id;
        //info("user_id". $user_id);

        foreach ($data as $row) {
            $current = $row;
            $current->site_id = $targetSiteId;
            $current->tenant_id = $targetTenantId;
            $current->category_id = $targetCategoryId;
            $current->insert_by = $user_id;
            $current->update_by = $user_id;
            $current->approved_by = $user_id;
            $current->created_at = htcms_get_current_date();
            $current->updated_at = htcms_get_current_date();

            //Need to fetch module id based on module alias
            if($targetSiteId != $sourceSiteId) {
                $moduleAlias = Module::withoutGlobalScopes()->find($current->module_id, "alias")->alias;
                $where = array(array('site_id', '=', $targetSiteId), array('alias', '=', $moduleAlias));
                $current->module_id =  Module::withoutGlobalScopes()->where($where)->first("id")->id;
            }

            $newData[] = (array)$current;
        }

        //Delete old data
        try {
            DB::beginTransaction();
            DB::table("module_site")->where($toData)->delete();
            $inserted = DB::table("module_site")->insert($newData);
        } catch (\Exception $e) {
            DB::rollBack();
            return array("success"=>false, "message"=>$e->getMessage());
        }

        DB::commit();
        //info(json_encode($newData));
        //inesrt new
        return array("success"=>$inserted, "message"=>"Modules are copied for the category");

    }


}

