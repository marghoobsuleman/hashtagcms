<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use MarghoobSuleman\HashtagCms\Models\Theme;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;

class ThemeController extends BaseAdminController
{

    protected $dataFields = array(
        array("label" => "ID", "key" => "id"),
        array("label" => "Name", "key" => "name"),
        array("label" => "Alias", "key" => "alias"),
        array("label" => "Directory", "key" => "directory")
    );


    protected $actionFields = array("edit", "delete");

    protected $dataSource = Theme::class;

    protected $minResults = 1;

    protected $bindDataWithAddEdit = array("sites"=>array("dataSource"=>Site::class, "method"=>"all"));

    //private $request;

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request) {

        if(!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $rules = [
            "site_id" => "required|numeric",
            "name" => "required|max:60|string",
            "alias"=> ["required", "max:60", "string",
                Rule::unique('themes')->where(function ($query) use ($request) {
                    $query->where('site_id', $request->input("site_id"))
                        ->where('alias', $request->input("alias"));
                })],
            "directory" => "required|max:60|string",
            "body_class" => "nullable|max:255|string",
            "img_preview" => "nullable|file",
            "skeleton" => "required"
        ];


        if ($request->input("id") > 0) {
            $rules["alias"] = Rule::unique('themes')->where(function ($query) use ($request) {
                $query->where('site_id', $request->input("site_id"))
                    ->where('alias', $request->input("alias"))
                    ->where('id', '<>', $request->input("id"));
            });
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $module_name = request()->module_info->controller_name;

        $saveData["name"] = $data["name"];
        $saveData["alias"] = strtoupper($data["alias"]);
        $saveData["site_id"] = $data["site_id"];
        $saveData["skeleton"] = $data["skeleton"];
        $saveData["directory"] = Str::kebab(strtolower($data["directory"]));
        $saveData["body_class"] = $data["body_class"];

        $saveData["header_content"] = $data["header_content"];
        $saveData["footer_content"] = $data["footer_content"];


        //update Image
        $img_preview = $this->upload($module_name, request()->file("img_preview"));

        if($img_preview!=NULL) {
            $saveData["img_preview"] = $img_preview;
        }

        if($data["img_preview_deleted"] != "0") {
            $saveData["img_preview"] = "";
        }

        $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);

        if($data["actionPerformed"]=="edit") {
            $where = $data["id"];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData,$where);
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
