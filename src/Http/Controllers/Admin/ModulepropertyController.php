<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\ModuleProp;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;
use MarghoobSuleman\HashtagCms\Http\Controllers\Admin\BaseAdminController;
use MarghoobSuleman\HashtagCms\Models\Module;
use MarghoobSuleman\HashtagCms\Models\Platform;

class ModulepropertyController extends BaseAdminController
{
    protected $dataFields = ['id','name','lang.value as value', 'group_name', 'module.alias', 'platform.name', 'updated_at'];

    protected $dataSource = ModuleProp::class;

    protected $dataWith = ['lang', 'module', 'platform'];

    protected $actionFields = array("edit", "delete"); //This is last column of the row

    protected $bindDataWithAddEdit = array(
                                    "modules"=>array("dataSource"=>Module::class, "method"=>"all", "params"=>array("id", "alias")),
                                    "platforms"=>array("dataSource"=>Platform::class, "method"=>"all", "params"=>array("id", "name")),
                                    );


    public function store(Request $request) {

            //$module_name = $request->module_info->controller_name;

             if(!$this->checkPolicy('edit')) {
                  return htcms_admin_view("common.error", Message::getWriteError());
             }
             $rules = ["module_id" => "required",
                         "site_id" => "required",
                         "platform_id" => "required",
                         "name" => "required|max:100|string",
                         "group_name" => "nullable|max:100|string",
                         "value" => "required|max:500|string"
                    ];

             $validator = Validator::make($request->all(), $rules);



             if ($validator->fails()) {

                 return redirect()->back()
                     ->withErrors($validator)
                     ->withInput();
             }

            $data = $request->all();

            $saveData['group_name'] = $data["group_name"];
            $saveData['name'] =  $data["name"];
            $saveData['site_id'] = $data["site_id"];
            $saveData['updated_at'] = htcms_get_current_date();

            $updateInAllLanguages = (isset($data["update_in_all_language"]) && (string)$data["update_in_all_language"]==="1") ? true : false;

            //lang
            $langData["value"] = $data["value"];
            $langData['updated_at'] = htcms_get_current_date();

            $arrLangData = array("data"=>$langData);

            if ($data["actionPerformed"] === 'edit') {
                $saveData['platform_id'] = $data['platform_id'];
                $saveData['module_id'] = $data['module_id'];

                $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);

                $where = $data["id"];
                //This is in base controller
                $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData, $where, $updateInAllLanguages);
            } else {

                $saveData['created_at'] = htcms_get_current_date();

                //it will always be in array
                $allPlatforms = $data['platform_id'];
                $allModules = $data['module_id'];
                //insert in all platform
                if(is_array($allPlatforms)) {
                    foreach ($allPlatforms as $current_platform_id) {
                        $saveData['platform_id'] = $current_platform_id;
                        //check if there is multiple modules
                        if(is_array($allModules)) {
                            foreach ($allModules as $current_module_id) {
                                $saveData['module_id'] = $current_module_id;
                                $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);
                                $savedData = $this->saveDataWithLang($arrSaveData, $arrLangData);
                            }
                        }
                    }
                }
            }

            $viewData["id"] = $savedData["id"];
            $viewData["saveData"] = $data;
            $viewData["backURL"] = $data["backURL"];
            $viewData["isSaved"] = $savedData["isSaved"];

            return htcms_admin_view("common.saveinfo", $viewData);
        }
}
