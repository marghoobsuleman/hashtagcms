<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Core\Traits\Admin\BlogPageCommon;
use MarghoobSuleman\HashtagCms\Models\Tenant;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Page;
use MarghoobSuleman\HashtagCms\Models\User as UserData;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;


class BlogController extends BaseAdminController
{
    use BlogPageCommon;

    protected $dataFields = ['id','lang.title as title','lang.name as name', 'link_rewrite', 'read_count', 'publish_status'];

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

        $rules = $this->getRulesArray();

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $module_name = request()->module_info->controller_name;

        $viewData = $this->saveBlogPageData($data, $module_name);

        return htcms_admin_view("common.saveinfo", $viewData);
    }



}

