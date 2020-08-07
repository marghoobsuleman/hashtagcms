<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\Tenant;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Page;
use MarghoobSuleman\HashtagCms\Models\User as UserData;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;


class BlogController extends BaseAdminController
{
    protected $dataFields = ['id','lang.title as title','lang.name as name', 'link_rewrite', 'publish_status'];

    protected $dataSource = Page::class;

    protected $dataWith = ['lang'];

    protected $actionFields = array("edit", "delete"); //This is last column of the row

    protected $dataWhere = array(array('field'=>'content_type', 'operator'=>'=', 'value'=>'blog'));

    protected $users;

    protected $bindDataWithAddEdit = array("tenants" => array("dataSource" => Tenant::class, "method" => "all"),
                                        "targetTypes" => array("dataSource" => Category::class, "method" => "getTargetType"),
                                        "relationTypes" => array("dataSource" => Category::class, "method" => "getLinkRelationType"),
                                        "contentCategories" => array("dataSource" => Category::class, "method" => "parentOnlyDynamic"),
                                        "contentTypes" => array("dataSource" => Page::class, "method" => "getContentTypes"),
                                        "menuPlacements" => array("dataSource" => Page::class, "method" => "getMenuPlacements"),
                                        "authors" => array("dataSource" => UserData::class, "method" => "all"),
                                        "defaultContentType" => "blog",
                                        "defaultCategory"=>0
                                        );


    /**
     * Change link rewrite if you going to change url instead of blog
     */
    protected function preEdit() {
        $res = Category::where("link_rewrite", "=", "blog")->first("id");
        $this->bindDataWithAddEdit['defaultCategory'] = $res->id;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request) {

        if(!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $rules = [
            "category_id" => "nullable|numeric",
            "site_id" => "required|numeric",
            "tenant_id" => "nullable|numeric",
            "alias" => "nullable|max:60|string",
            "microsite_id" => "nullable|numeric",
            "exclude_in_listing" => "nullable|integer",
            "content_type" => "required",
            "position" => "nullable|integer",
            "link_rewrite" => "required|max:255|string",
            "link_navigation" => "nullable|max:255|string",
            "menu_placement" => "nullable|max:100|string",
            "insert_by" => "required|numeric",
            "update_by" => "required|numeric",
            "publish_status" => "nullable|integer",
            "lang_name" => "required|max:128|string",
            "lang_title" => "required|max:128|string",
            "lang_page_content" => "required",
            "lang_active_key" => "nullable|max:128|string",
            "lang_meta_title" => "nullable|max:160|string",
            "lang_meta_keywords" => "nullable|max:255|string",
            "lang_meta_description" => "nullable|max:255|string",
            "lang_meta_robots" => "nullable|max:255|string",
            "lang_meta_canonical" => "nullable|max:255|string"
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $module_name = request()->module_info->controller_name;

        $saveData["alias"] = $data["alias"];
        $saveData["category_id"] = $data["category_id"];
        $saveData["content_type"] = $data["content_type"];

        $saveData["site_id"] =  $data["site_id"] ?? htcms_get_siteId_for_admin();
        $saveData["tenant_id"] = $data["tenant_id"];

        $saveData["link_navigation"] = $data["link_navigation"];
        $saveData["link_rewrite"] = $data["link_rewrite"];

        $saveData["menu_placement"] = $data["menu_placement"];
        $saveData["exclude_in_listing"] = $data["exclude_in_listing"] ?? 0;

        $saveData["header_content"] = $data["header_content"];
        $saveData["footer_content"] = $data["footer_content"];

        $saveData["position"] = $this->dataSource::count()+1;

        $saveData["enable_comments"] = $data["enable_comments"] ?? 0;
        $saveData["publish_status"] = $data["publish_status"] ?? 0;
        $saveData["required_login"] = $data["required_login"] ?? 0;

        $langData["name"] = $data["lang_name"];
        $langData["title"] = $data["lang_title"];
        $langData["active_key"] = $data["lang_active_key"];
        $langData["target"] = $data["lang_target"];
        $langData["link_relation"] = $data["lang_link_relation"];
        $langData["page_content"] = $data["lang_page_content"];
        $langData["description"] = $data["lang_description"];
        $langData["meta_title"] = $data["lang_meta_title"];
        $langData["meta_keywords"] = $data["lang_meta_keywords"];
        $langData["meta_description"] = $data["lang_meta_description"];
        $langData["meta_robots"] = $data["lang_meta_robots"];
        $langData["meta_canonical"] = $data["lang_meta_canonical"];

        //date
        $saveData["updated_at"] = htcms_get_current_date();
        $langData["updated_at"] = htcms_get_current_date();
        if($data["actionPerformed"] !== "edit") {
            $saveData["insert_by"] = $data["insert_by"];
            $saveData["created_at"] = htcms_get_current_date();
            $langData["created_at"] = htcms_get_current_date();
        }

        $saveData["update_by"] = Auth::user()->id;

        $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);
        $arrLangData = array("data"=>$langData);
        //
        if($data["actionPerformed"]=="edit") {
            $where = $data["id"];

            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {

            //This is in base controlle
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        // print_r($arrLangData);
        // exit;
        //
        $viewData["id"] = $savedData["id"];
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];

        return htcms_admin_view("common.saveinfo", $viewData);
    }

}

