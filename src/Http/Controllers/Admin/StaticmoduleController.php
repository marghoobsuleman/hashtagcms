<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\StaticModuleContent;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;

class StaticmoduleController extends BaseAdminController
{
    protected $dataFields = ['id','lang.title as title','alias','updated_at'];

    protected $dataSource = StaticModuleContent::class;

    protected $dataWith = ['lang'];

    protected $actionFields = array("edit", "delete"); //This is last column of the row

    protected $bindDataWithAddEdit = array("sites"=>array("dataSource"=>Site::class, "method"=>"all"));


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
            "alias" => "required|max:60|string",
            "update_by" => "required|numeric",
            "lang_title" => "required|max:255|string",
            "lang_content" => "required"
        ];

        if($request->input("id")==0) {

            $rules["insert_by"] = "required|numeric";

        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();


        $saveData["alias"] = strtoupper($data["alias"]);
        $saveData["site_id"] = $data["site_id"];

        $saveData["update_by"] = $data["update_by"];

        if(!isset($data["id"]) || $data["id"] == 0) {
            $saveData["insert_by"] = $data["insert_by"];
        }

        $langData["lang_id"] = $data["lang_id"] ?? 1;

        $langData["title"] = $data["lang_title"];
        $langData["content"] = $data["lang_content"];

        $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);

        $arrLangData = array("data"=>$langData);

        if($data["actionPerformed"]=="edit") {

            $where = $data["id"];
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        $viewData["id"] = $savedData["id"];;
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];

        return htcms_admin_view("common.saveinfo", $viewData);
    }
}
