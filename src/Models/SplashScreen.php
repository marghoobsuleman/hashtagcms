<?php

namespace MarghoobSuleman\HashtagCms\Models;

/**
 * For Mobile use only
 * Class SplashScreen
 * Desc: Get Splash Screen Data
 *
 */


class SplashScreen extends AdminBaseModel
{

    protected $guarded = array();

    /**
     * Init for mobile splash screen
     * @param null $context
     * @param string $tenant
     * @return array
     */
    public static function loadData($context=null, $tenant="android") {

        $data = array();

        $site = Site::with(['lang'])->where("context", $context)->withoutGlobalScopes()->first();

        if($site->count() == 0) {

        } else {
            $tenant = Tenant::where("link_rewrite", "=", $tenant)->first();

            $category_id = $site->category_id;
            $lang_id = $site->lang_id;
            $country_id = $site->country_id;
            $currency_id = 1; //@todo: Make it in site default.
            $tenant_id = $tenant->id;
            $site_id = $site->id;

            $data["site"] = $site;
            $data["defaults"] = array("category_id"=>$category_id, "lang_id"=>$lang_id, "country_id"=>$country_id, "currency_id"=>$currency_id);
            $data["languages"] = Lang::all();
            $data["tenant"] = $tenant;
            $data["props"] = SiteProp::where("site_id", "=", $site_id)
                                        ->where("tenant_id", "=", $tenant_id)
                                        ->where("is_public", "=", 1)
                                        ->get();

        }

        return $data;

    }

}
