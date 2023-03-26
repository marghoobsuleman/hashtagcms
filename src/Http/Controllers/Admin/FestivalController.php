<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\Festival;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;


class FestivalController extends BaseAdminController
{
    protected $dataFields = ['id',
        array("label" => "Image", "key" => "image", "isImage" => true),
        'body_css', 'header_css', 'footer_css', 'start_date', 'end_date', 'publish_status', 'updated_at'];

    protected $dataSource = Festival::class;

    protected $dataWith = '';

    protected $actionFields = array("edit", "delete"); //This is last column of the row


    // protected $minResults = 1; //this will disable delete when record count is one
    /*
    //This will be added after add/edit etc action fields
    protected $moreActionFields = array(
            array("label"=>"Show all info",
                "css"=>"js_ajax",
                "icon_css"=>"fa fa-info-circle",
                "hrefAttributes"=>["data-info"=>"cmslog", "data-editable"=>false, "data-excludefields"=>["user", "module"]],
                "action"=>"showinfo",
                "action_append_field"=>"id"
                ),
                 array("label"=>"Site Settings",
                            "icon_css"=>"fa fa-cogs",
                            "action"=>"settings",
                            "action_append_field"=>"id")
        );
    */

    /*
    //This is action bar items. (Add/Search bar)
    protected $moreActionBarItems = array(
            array("label"=>"Clone Site",
                "as"=>"icon",
                "icon_css"=>"fa fa-copy", "action"=> "site/copysite"
            )
        );
    */

    //Get data for editing.
    /*protected $bindDataWithAddEdit = array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                        "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"));
                                        */


    public function store(Request $request)
    {

        $module_name = $request->module_info->controller_name;

        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }
        $rules = ["site_id" => "required",
            "image" => "nullable|file",
            "body_css" => "nullable|max:255|string",
            "header_css" => "nullable|max:255|string",
            "footer_css" => "nullable|max:255|string",
            "start_date" => "required|date",
            "end_date" => "required|date"
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $image = $this->upload($module_name, request()->file("image"));

        if ($image != NULL) {
            $saveData["image"] = $image;
        }

        $saveData["lottie"] = $data["lottie"];
        $saveData["body_css"] = $data["body_css"];
        $saveData["header_css"] = $data["header_css"];
        $saveData["footer_css"] = $data["footer_css"];
        $saveData["start_date"] = $data["start_date"];
        $saveData["end_date"] = $data["end_date"];
        $saveData["publish_status"] = $data["publish_status"] ?? 0;
        $saveData["site_id"] = $data["site_id"];
        $saveData["updated_at"] = htcms_get_current_date();

        if ($data["actionPerformed"] !== "edit") {
            $saveData["created_at"] = htcms_get_current_date();
        }

        $arrSaveData = array("model" => $this->dataSource, "data" => $saveData);

        if ($data["actionPerformed"] == "edit") {

            $where = $data["id"];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
        }

        $viewData["id"] = $savedData["id"];;
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];

        return htcms_admin_view("common.saveinfo", $viewData);
    }
}
