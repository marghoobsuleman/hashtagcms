<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Core\Traits\Admin\BlogPageCommon;
use MarghoobSuleman\HashtagCms\Models\Tenant;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Page;
use MarghoobSuleman\HashtagCms\Models\User as UserData;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;



class PageController extends BaseAdminController
{
    use BlogPageCommon;

    protected $dataFields = ['id','lang.title as title','lang.name as name', 'category.link_rewrite as category', 'link_rewrite', 'read_count', 'publish_status', 'updated_at'];

    protected $dataSource = Page::class;

    protected $dataWith = ['lang', 'category'];

    protected $actionFields = array("edit", "delete"); //This is last column of the row

    protected $dataWhere = array(array('field'=>'content_type', 'operator'=>'=', 'value'=>'page'));

    protected $users;

    protected $bindDataWithAddEdit = array("tenants" => array("dataSource" => Tenant::class, "method" => "all"),
                                        "targetTypes" => array("dataSource" => Category::class, "method" => "getTargetType"),
                                        "relationTypes" => array("dataSource" => Category::class, "method" => "getLinkRelationType"),
                                        "contentCategories" => array("dataSource" => Category::class, "method" => "parentOnlyDynamic"),
                                        "contentTypes" => array("dataSource" => Page::class, "method" => "getContentTypes"),
                                        "menuPlacements" => array("dataSource" => Page::class, "method" => "getMenuPlacements"),
                                        "authors" => array("dataSource" => UserData::class, "method" => "all"),
                                        "defaultContentType" => "page",
                                        );

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

    /**
     * @param Request $request
     * @return mixed
     */
    public function getParentCategory(Request $request) {

        $data = $request->all();
        $content_type = $data['content_type'];
        $category_id = $data['category_id'];

        $where = array(array("content_type", "=", $content_type),
            array("category_id", "=", $category_id),
            array("site_id", "=", htcms_get_siteId_for_admin())
        );
        return Page::where($where)->with('lang')->get();

    }
}
