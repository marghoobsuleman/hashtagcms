<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\Platform;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;

class PlatformController extends BaseAdminController
{
    //

    protected $dataFields = array("id", "name", "link_rewrite", "updated_at");

    protected $dataWith = ['site'];

    protected $dataSource = Platform::class;

    protected $minResults = 1;

    protected $actionFields = array("edit", "delete"); //This is last column of the row



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
            "link_rewrite" => array("required",
                "max:100",
                "string",
                "regex:/^\w{1,}$/")
        ];

        if($request->input("id")==0) {

            array_push($rules["link_rewrite"], "unique:platforms,link_rewrite");

        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData["name"] = $data["name"];
        $saveData["link_rewrite"] = strtolower($data["link_rewrite"]);

        $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);
        if($data["actionPerformed"]=="edit") {
            $where = $data["id"];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData,$where);
        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
        }
        $viewData["id"] = $savedData["id"];
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];

        return htcms_admin_view("common.saveinfo", $viewData);
    }
}
