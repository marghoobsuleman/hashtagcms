<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\CategoryLang;
use MarghoobSuleman\HashtagCms\Models\CategorySite;
use MarghoobSuleman\HashtagCms\Models\Country;
use MarghoobSuleman\HashtagCms\Models\CountryLang;
use MarghoobSuleman\HashtagCms\Models\Currency;
use MarghoobSuleman\HashtagCms\Models\Hook;
use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\SiteProp;
use MarghoobSuleman\HashtagCms\Models\StaticModuleContent;
use MarghoobSuleman\HashtagCms\Models\StaticModuleContentLang;
use MarghoobSuleman\HashtagCms\Models\Tenant;
use MarghoobSuleman\HashtagCms\Models\Theme;
use MarghoobSuleman\HashtagCms\Models\Zone;
use MarghoobSuleman\HashtagCms\Models\Module;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;


class SiteController extends BaseAdminController
{

    protected $dataFields = array("id", "name", "lang.title", "context", "logo");

    protected $dataSource = Site::class;

    protected $dataWith = ['lang', 'category', 'theme', 'tenant'];

    protected $minResults = 1;

    protected $actionFields = array("edit", "delete"); //This is last column of the row

    protected $bindDataWithAddEdit = array(
                                        "languages"=>array("dataSource"=>Lang::class, "method"=>"combo"),
                                        "countries"=>array("dataSource"=>CountryLang::class, "method"=>"all")
                                    );

    protected $moreActionFields = array(
        array("label"=>"Site Settings",
            "icon_css"=>"fa fa-cogs",
            "action"=>"settings",
            "action_append_field"=>"id")
    );


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request) {

        if(!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $rules = [
            "name" => "required|max:100|string",
            "category_id" => "nullable|numeric",
            "theme_id" => "nullable|numeric",
            "lang_id" => "nullable|numeric",
            "under_maintenance" => "nullable|integer",
            "domain" => "required|max:255|string",
            "context" => "required|max:40|string",
            "favicon" => "nullable|max:255|file",
            "lang_count" => "nullable|numeric",
            "lang_title" => "required|max:255|string"
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $module_name = request()->module_info->controller_name;

        $saveData["name"] = $data["name"];
        $saveData["domain"] = $data["domain"];
        $saveData["context"] = $data["context"];
        $saveData["favicon"] = "";
        $saveData["under_maintenance"] = $data["under_maintenance"] ?? 0;

        $saveData["category_id"] = $data["category_id"] ?? 0;
        $saveData["theme_id"] = $data["theme_id"] ?? 0;
        $saveData["lang_id"] = $data["lang_id"] ?? 0;
        $saveData["tenant_id"] = $data["tenant_id"] ?? 0;
        $saveData["country_id"] = $data["country_id"] ?? 0;


        $langData["title"] = $data["lang_title"];

        $icon = $this->upload($module_name, request()->file("favicon"));

        if($icon != NULL) {

            $saveData["favicon"] = $icon;
        }

        $arrLangData = array("data"=>$langData);

        $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);

        if($data["actionPerformed"]=="edit") {

            $where = $data["id"];
            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {

            //At the time of creation

            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);

        }

        $viewData["id"] = $savedData["id"];;
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];

        return htcms_admin_view("site.saveinfo", $viewData);
    }


    /**
     * Get settings setting
     * @param int $id
     * @param string $tab
     * @return mixed
     */
    public function settings($id = 1, $tab='tenants')
    {

        if(!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $siteInfo = Site::allInfo($id);


        $tab = (empty($tab)) ? "tenants" : $tab;

        $viewData["siteInfo"] = $siteInfo;
        $viewData["tabs"] = array("Tenants", "Hooks", "Languages", "Zones",
                                    "Countries", "Currencies", "Modules",
                                    "Static Modules", "Themes", "Categories", "Site Properties");

        $tab = str_replace(" ", "", strtolower($tab));
        $toBeSelected = Str::singular($tab);

        $selectedData = $viewData["siteInfo"]->$toBeSelected; //lazy load

        $data = array();
        $viewName = "settings";
        $tabName = Str::plural($tab);
        switch ($tabName) {
            case 'countries':

                $data = array("label"=>"Countries", "data"=>Country::with("lang:country_id,name")->get(["id"]));

                break;
            case 'zones':

                $data = array("label"=>"Zones", "data"=>Zone::all(["id", "name"]));

                break;
            case 'tenants':

                $data = array("label"=>"Tenants", "data"=>Tenant::all(["id", "name"]));

                break;
            case 'languages':

                $data = array("label"=>"Languages",
                    "data"=>Lang::all(["id", "name"])
                );

                break;
            case 'hooks':

                $data = array("label"=>"Hooks", "data"=>Hook::all(["id", "name"]));

                break;
            case 'currencies':

                $data = array("label"=>"Currency", "data"=>Currency::all(["id", "name"]));

                break;
            case 'modules':

                $viewName = "copier";
                $selectedData = Module::withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();

                $message = ($selectedData->count() > 0) ? "Below modules are already available in your site." : "You can copy module from desired site. Choose site option, select and click on Add Selected.";

                $data = array("label"=>"Modules",
                    "data"=>array("allSites"=>Site::with('lang')->get()),
                    "message"=>$message
                    );

                break;
            case 'staticmodules':
                $viewName = "copier";
                $selectedData = StaticModuleContent::withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();

                $message = ($selectedData->count() > 0) ? "Below static modules are already available in your site." : "You can copy static module from desired site. Choose site option, select and click on Add Selected.";

                $data = array("label"=>"StaticModules",
                    "data"=>array("allSites"=>Site::with('lang')->get()),
                    "message"=>$message
                    );

                break;
            case 'siteproperties':
                $viewName = "copier";
                $selectedData = SiteProp::withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();
                $message = ($selectedData->count() > 0) ? "Below properties are already available in your site." : "You can copy properties from desired site. Choose site option, select and click on Add Selected.";
                $data = array("label"=>"SiteProperties",
                    "data"=>array("allSites"=>Site::with('lang')->get()),
                    "message"=>$message
                    );

                break;
            case 'themes':

                $viewName = "copier";
                $selectedData = Theme::where('site_id', '=', $siteInfo->id)->withoutGlobalScopes()->get();
                //info($siteInfo->id. " " .json_encode($selectedData));
                $message = ($selectedData->count() > 0) ? "Below themes are already available in your site." : "You can copy theme from desired site. Choose site option, select and click on Add Selected.";
                $data = array("label"=>"Themes",
                    "data"=>array("allSites"=>Site::with('lang')->get()),
                    "message"=>$message
                    );

                break;
            case 'categories':
                $viewName = "copier";
                $selectedData = Category::with('lang')->withoutGlobalScopes()->where('site_id', '=', $siteInfo->id)->get();

                //info(json_encode($selectedData));
                $path = htcms_admin_path("category/settings");
                $message = ($selectedData->count() > 0) ? "Below categories are already available in your site." : "You can copy categories from desired site. Choose site option, select and click on Add Selected.";
                $data = array("label"=>"Categories",
                                "data"=>array("allSites"=>Site::with('lang')->get()),
                                "message"=>$message);
                break;
        }

        $viewData["selectedData"] = $selectedData;
        $viewData["activeTab"] = Str::plural(strtolower($tab));

        $viewData["allData"] = $data;
        $viewData["backURL"] = $this->getBackURL();

        //return $viewData;
        return $this->viewNow("site.".$viewName, $viewData);
    }

    /**
     * Save settings in current site. If you have pivot table
     * Find relationa and attach and detach from the site
     * @return mixed
     */
    public function saveSettings() {
        $data = request()->all();
        $site_id = $data["site_id"];
        $key = $data["key"];
        $ids = $data["ids"];
        $action = $data["action"];

        $site = Site::find($site_id);

        if($action == "add") {
            return $site->attachThings($key, $ids);
        }

        return $site->detachThings($key, $ids);


    }


    /**
     * Check if module is exist
     * @param array $data
     * @param string $compare
     * $param string $compareKey - row key
     * @return bool
     */
    private function isExist($data=array(), $compare='', $compareKey='') {
        foreach ($data as $key=>$val) {
            if($compare === $val[$compareKey]) {
                return TRUE;
            }
        }
        return FALSE;
    }


    /**
     * Remove settings. such as modules, categories, themes etc
     * @return mixed
     */
    public function removeSettings() {
        $data = request()->all();

        $site_id = $data["site_id"];
        $ids = $data["ids"];
        $removeWhat = $data["type"];
        //To make it more readable - let use switch.

        $withData = [];
        switch (strtolower($removeWhat)) {
            case "modules":
                $source = Module::class;
                break;
            case "categories":
                $withData = ['lang'];
                $source = Category::class;
                break;
            case "staticmodules":
                $source = StaticModuleContent::class;
                break;
            case "siteproperties":
                $source = SiteProp::class;
                break;
        }

        $rData["deleted"] = $source::withoutGlobalScopes()->find($ids)->each->forceDelete();
        $rData["siteData"] = $source::with($withData)->withoutGlobalScopes()->where('site_id', '=', $site_id)->get(); //get fresh one

        return $rData;

    }

    /**
     * @param array $data
     * @param $source
     * @param $langSource
     * @param string $idField
     * @param null $tabs
     * @return bool
     */
    private function saveSettingWithLangs($data=array(), $source, $langSource, $idField='', $tabs=null, $toSite=null) {
        $inserted = FALSE;

        //info(json_encode($data));
        //return false;

        DB::beginTransaction();

        foreach ($data as $key=>$val) {

            $oldId = $val["id"];

            //get all languages for a category
            $langData = $langSource::where($idField, "=", $oldId)->withoutGlobalScopes()->get()->toArray();

            unset($val["id"]);
            unset($val["lang"]); //if exists


            $content = new $source;
            foreach ($val as $k=>$v) {
                $content->$k = $v;
            }
            $inserted = $content->save();

            //Need to copy category_sites
            if($tabs == "categories") {
                $siteData = CategorySite::where("category_id", "=", $oldId)->get()->toArray();
                $sData = [];
                foreach ($siteData as $s=>$sd) {
                    $sd['category_id'] = $content->id;
                    $sd['site_id'] = $content->site_id;
                    $sd['theme_id'] = Theme::getThemeIdThroughSite((int)$sd['theme_id'], (int)$val['site_id']);
                    $sd['created_at'] = htcms_get_current_date();
                    $sd['updated_at'] = htcms_get_current_date();
                    $sData[] = $sd;
                }
                try {
                    //info($sData);
                    $inserted = CategorySite::insert($sData);
                } catch (\Exception $e) {
                    DB::rollBack();

                    $inserted = false;
                }
            }

            foreach ($langData as $lKey=>$lVal) {

                $lVal[$idField] = $content->id;

                $lVal['created_at'] = htcms_get_current_date();
                $lVal['updated_at'] = htcms_get_current_date();

                try {
                    $inserted = $content->lang()->create($lVal);
                } catch (\Exception $e) {
                    DB::rollBack();
                    $inserted = false;
                }

            }

        }

        DB::commit();
        return $inserted;
    }

    /**
     * Copy things from source site to target site
     * @return array
     */
    public function copySettings() {

        $data = request()->all();
        $fromSite = $data["fromSite"];
        $toSite = $data["toSite"];
        $copyWhat = $data["type"];
        $toSite_id = $toSite["site_id"];

        $resetId = TRUE;
        $compareKey = "alias";
        switch (strtolower($copyWhat)) {
            case "modules":
                $source = Module::class;
            break;
            case "staticmodules":
                $source = StaticModuleContent::class;
                $resetId = FALSE;
                break;
            case "siteproperties":
                $source = SiteProp::class;
                break;
            case "themes":
                $source = Theme::class;
                break;
            case "categories":
                $source = Category::class;
                $compareKey = "link_rewrite";
                $resetId = FALSE;
                break;
            default:
                return array("error"=>true, "message"=>"Don't know what to do.");
                break;
        }

        $existing = $source::where('site_id', '=', $toSite_id)->withoutGlobalScopes()->get(); //fetch existing

        $data = $fromSite["data"];

        $toBeInserted = array();
        $alreadyExist = array();

        if(count($existing) > 0) {
            //filter
            foreach($data as $key=>$val) {

                if($this->isExist($existing, $val[$compareKey], $compareKey) === FALSE) {
                    $toBeInserted[] = $val;
                } else {
                    $alreadyExist[] = $val;
                }
            }

        } else {
            $toBeInserted = $data;
        }

        //return $toBeInserted;

        //remove some of keys and add site id
        $finalData = array();
        if(sizeof($toBeInserted) > 0) {
            foreach ($toBeInserted as $key=>$val) {
                $finalData[$key] = $val;
                $finalData[$key]["site_id"] = $toSite_id;

                if($resetId === TRUE) {
                    unset($finalData[$key]["id"]);
                }
                unset($finalData[$key]["selected"]);
                unset($finalData[$key]["created_at"]);
                unset($finalData[$key]["updated_at"]);

                $finalData[$key]["created_at"] = htcms_get_current_date();
                $finalData[$key]["updated_at"] = htcms_get_current_date();
            }
        }
        $inserted = FALSE;
        //return $finalData;
        $msg = "";
        $withData = [];
        if(sizeof($finalData) > 0) {
            DB::beginTransaction();
            try {
                switch (strtolower($copyWhat)) {
                    case "modules":
                        $inserted = Module::insert($finalData);
                        break;
                    case "staticmodules":
                        $inserted =  $this->saveSettingWithLangs($finalData, StaticModuleContent::class, StaticModuleContentLang::class, "static_module_content_id");
                        break;
                    case "siteproperties":
                        $inserted =  SiteProp::insert($finalData);;
                        break;
                    case "themes":
                        $inserted = Theme::insert($finalData);
                        break;
                    case "categories":
                        $withData = ['lang'];
                        $inserted =  $this->saveSettingWithLangs($finalData, Category::class, CategoryLang::class, "category_id", "categories", $toSite);
                        break;
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $msg = $e->getMessage() . " @ ".$e->getLine(). " fileName: ".$e->getFile();
                $inserted = false;
            }
            DB::commit();
        }

        //info("toSite_id: ".$toSite_id);
        $rData["siteData"] = $source::with($withData)->where('site_id', '=', $toSite_id)->withoutGlobalScopes()->get(); //get fresh one
        $rData["inserted"] = $inserted;
        $rData["copied"] = $finalData;
        $rData["ignored"] = $alreadyExist;
        $rData["message"] = $msg;

        return $rData;
    }

    /**
     * Get all sites
     * @return Site[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllSite() {
        return Site::all();
    }

    /**
     * Get tabs data by site
     * @param null $site_id
     * @return array
     */
    public function getBySite($site_id=NULL, $key='modules') {

        if($site_id != NULL) {
            switch (strtolower($key)) {
                case "modules":
                    $data = Module::where('site_id', '=', $site_id)->get();;
                    break;
                case "staticmodules":
                    $data = StaticModuleContent::withoutGlobalScopes()->where('site_id', '=', $site_id)->get();
                    break;
                case "siteproperties":
                    $data = SiteProp::withoutGlobalScopes()->where('site_id', '=', $site_id)->get();
                    break;
                case "themes":
                    $data = Theme::where('site_id', '=', $site_id)->get();;
                    break;
                case "categories":
                    $data = Category::with('lang')->withoutGlobalScopes()->where('site_id', '=', $site_id)->get();
                    break;
            }
            return $data;
        }

        return array("error"=>true, "message"=>"Site id is not provided");
    }

}
