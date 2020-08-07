<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\Hook;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;

class HookController extends BaseAdminController
{
    protected $dataFields = ['id','site.name as site_name','name','alias','description','created_at','updated_at'];

    protected $dataSource = Hook::class;

    protected $dataWith = ['site'];

    protected $actionFields = array("edit", "delete"); //This is last column of the row

    protected $bindDataWithAddEdit = array("sites"=>array("dataSource"=>Site::class, "method"=>"all", "params"=>["id", "name"]));


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request) {

        if(!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $rules = [
            "name" => "required|max:64|string",
            "alias" => "required|max:64|string"
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();


        $saveData["name"] = $data["name"];
        $saveData["alias"] = $data["alias"];
        $saveData["description"] = $data["description"];
       // $saveData["site_id"] = $data["site_id"];

        //date
        $saveData["updated_at"] = htcms_get_current_date();
        if($data["actionPerformed"] !== "edit") {
            $saveData["created_at"] = htcms_get_current_date();
        }

        $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);

        if($data["actionPerformed"]=="edit") {

            $where = $data["id"];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where);

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
            //$savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
        }

        $viewData["id"] = $savedData["id"];;
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];

        return htcms_admin_view("common.saveinfo", $viewData);
    }
}
