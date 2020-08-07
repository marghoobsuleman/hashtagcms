<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\Festival;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;


class FestivalController extends BaseAdminController
{
    protected $dataFields = ['id','start_date','site_id','end_date','publish_status','created_at','updated_at'];

    protected $dataSource = Festival::class;

    protected $dataWith = '';

    protected $actionFields = array("edit", "delete"); //This is last column of the row

    /*protected $bindDataWithAddEdit = array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                        "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"));*/



    /**
     * @todo: need to work on this
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request) {

        if(!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $rules = ["site_id" => "required|numeric",
            "image" => "nullable|max:255|string",
            "body_css" => "nullable|max:255|string",
            "header_css" => "nullable|max:255|string",
            "footer_css" => "nullable|max:255|string",
            "publish_status" => "nullable|integer"
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData["name"] = $data["name"];

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
