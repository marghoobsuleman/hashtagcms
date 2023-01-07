<?php

namespace MarghoobSuleman\HashtagCms\Core\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use MarghoobSuleman\HashtagCms\Core\Traits\Api\ErrorHandler;
use MarghoobSuleman\HashtagCms\Models\Theme;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\SiteProp;
use MarghoobSuleman\HashtagCms\Models\Country;
use MarghoobSuleman\HashtagCms\Models\CountryLang;
use MarghoobSuleman\HashtagCms\Models\CurrencySite;

class Site extends JsonResource
{
    use ErrorHandler;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $isWeb = true;
        $withModules = false;

        if($this->hasError($this->id) === TRUE) {

            return $this->handleError();

        }

        //Java model is already written and can't change so making the response as before
        $defaultCountry = array();
        $defaultCountry['country_id'] = $this->country_id;
        $defaultCountry['site_id'] = $this->id;
        $defaultCountry['lang'] = CountryLang::where('country_id','=',$this->country_id)->first();
        $defaultCountry['country'] = Country::where('id','=',$this->country_id)->first();

        return [
            "id"=> $this->id,
            "name"=>  $this->name,
            "category_id"=> $this->category_id,
            "theme_id"=> $this->theme_id,
            "platform_id"=> $this->platform_id,
            "lang_id"=> $this->lang_id,
            "under_maintenance"=> $this->under_maintenance,
            "domain"=> $this->domain,
            "context"=> $this->context,
            "favicon"=> $this->favicon,
            "lang_count"=> $this->lang_count,
            "site_props"=> SiteProp::where('site_id','=',$this->id)->withoutGlobalScopes()->get(),
            "default_country"=> $defaultCountry,
            "default_currency"=> CurrencySite::where('site_id','=',$this->id)->with('currency')->first(),
            "supported"=>array(
                "categories"=>Category::with('lang')->where('site_id', $this->id)->withoutGlobalScopes()->get(),
                "themes"=>Theme::where('site_id', '=', $this->id)->withoutGlobalScopes()->get(),
                "microsites"=>$this->when($isWeb, $this->microsite), //when it is web - will work on this
                "platforms"=>$this->platform,
                "languages"=>$this->language,
                "countries"=>$this->country,
                "zones"=>$this->zone,
                "hooks"=>$this->hook,
                "modules"=>$this->when($withModules, $this->module), //when someone wants module too
                "currencies"=>$this->currency
            )
        ];

    }
}
