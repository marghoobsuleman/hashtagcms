<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use MarghoobSuleman\HashtagCms\Models\CmsModule;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;
use MarghoobSuleman\HashtagCms\Models\QueryLogger;

class CmsmoduleController extends BaseAdminController
{

    protected $dataFields = array("id", "name", "sub_title", "controller_name", "updated_at");

    protected $actionFields = array("edit", "delete");

    protected $moreActionBarItems = array(array("label" => "Sort Modules",
        "as" => "icon", "icon_css" => "fa fa-sort",
        "action" => "cmsmodule/sort"));


    protected $dataSource = CmsModule::class;

    protected $bindDataWithAddEdit = array("cmsModules" => array("dataSource" => CmsModule::class, "method" => "parentOnly"));


    /**
     * @return mixed
     */
    public function store(Request $request)
    {
        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $rules = [
            "name" => "required|max:255",
            "controller_name" => "required|max:255",
            "sub_title" => "required|max:100",
            "icon_css" => "max:255",
            "parent_id" => "nullable|numeric",
            "position" => "numeric"
        ];

        if ($request->input("id") == 0) {
            $rules["controller_name"] = $rules["controller_name"] . "|unique:cms_modules";
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $saveData["name"] = $data["name"];
        $saveData["sub_title"] = $data["sub_title"];
        $saveData["controller_name"] = $data["controller_name"];

        $saveData["parent_id"] = ($data["parent_id"] == "") ? 0 : $data["parent_id"];

        $saveData["icon_css"] = $data["icon_css"];
        $saveData["list_view_name"] = $data["list_view_name"];
        $saveData["edit_view_name"] = $data["edit_view_name"];


        //date
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

        $viewData["id"] = $savedData["id"];
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];


        return htcms_admin_view("common.saveinfo", $viewData);

    }


    /**
     * Sort Modules
     * @param null $allModules
     * @return mixed
     */
    public function sort()
    {
        $allModules = CmsModule::getAdminModules();

        $viewData["backURL"] = $this->getBackURL();
        $viewData["data"] = $allModules;
        $viewData["fields"] = array("id" => "id", "label" => "name");
        return htcms_admin_view("common.sorting", $viewData);
        //return $allModules;
    }


    /**
     * @return array
     */
    public function updateIndex()
    {

        $a = array();
        $data = request()->all();
        QueryLogger::setLogginStatus(false);
        foreach ($data as $key => $posData) {
            if ($posData != null) {
                $where = $posData["where"]["id"];
                $saveData["position"] = $posData["position"];
                $arrSaveData = array("model" => $this->dataSource, "data" => $saveData);
                $savedData = $this->saveData($arrSaveData, $where);
                array_push($a, $posData);
            }
        }
        QueryLogger::setLogginStatus(true);
        return array("indexUpdated" => $a);
    }

    /**
     * @return mixed
     *
     */
    //@override
    public function create()
    {


        $backURL = $this->getBackURL(FALSE);
        $data["actionPerformed"] = "add";
        $data["backURL"] = $backURL;
        $data["results"]["name"] = "Adding New...";

        $extraData = array("allTables" => array("dataSource" => CmsModule::class, "method" => "getAllTables"));

        $extra = $this->getExtraDataForEdit($extraData, TRUE);

        $data = array_merge($data, $extra);


        return htcms_admin_view("cmsmodule.add", $data);
    }




    /********* Create Admin modules ***************/

    /**
     * Desc: Create Module
     * @return array
     */
    public function createModule(Request $request)
    {


        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $rules = [
            "name" => "required|max:255",
            "controller_name" => "required|max:255",
            "sub_title" => "required|max:100",
            "icon_css" => "max:255",
            "parent_id" => "nullable|numeric",
            "position" => "numeric"
        ];

        if ($request->input("id") == 0) {
            $rules["controller_name"] = $rules["controller_name"] . "|unique:cms_modules";
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            if ($request->ajax()) {
                $msg["errors"] = $validator->getMessageBag()->toArray();
                return response()->json($msg, 400);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $data = $request->all();

        $controller_name = $data["controller_name"];
        $validator_name = $data["validator_name"];

        $dataSource = $data["dataSource"];
        $dataSource = str_replace("::class", "", $dataSource);

        $dataFields = (!empty($data["selectedFields"])) ? join(",", $data["selectedFields"]) : "*";
        $dataWith = (!empty($data["dataWith"])) ? join(",", $data["dataWith"]) : "null";

        $createFiles = (isset($data["createFiles"]) && $data["createFiles"] == false) ? false : true;


        try {

            if ($createFiles == true) {

                Artisan::call("cms:controller", [
                    "name" => $controller_name,
                    "dataSource" => $dataSource,
                    "dataWith" => $dataWith,
                    "dataFields" => $dataFields
                ]);

                $relationModels = $data["relationModels"]["models"];
                $methods = "";

                foreach ($relationModels as $key => $model) {

                    $current = $model;
                    $model_name = str_replace("::class", "", $current["model"]);
                    $relationAlias = $current["relationAlias"];
                    $relationType = $current["relationType"];
                    $methods .= "$relationAlias,$relationType,$model_name~";
                }

                //remove last tilt
                if ($methods != "") {
                    $methods = rtrim($methods, '~');
                }

                Artisan::call("cms:model", [
                    "name" => $dataSource,
                    "methods" => $methods
                ]);

            }


            //Save in DB
            $saveData["name"] = $data["name"];
            $saveData["sub_title"] = $data["sub_title"];
            $saveData["controller_name"] = $controller_name;
            $saveData["parent_id"] = $data["parent_id"];
            $saveData["icon_css"] = $data["icon_css"];

            $saveData["sub_title"] = $data["sub_title"];
            $saveData["controller_name"] = $data["controller_name"];

            $saveData["list_view_name"] = $data["list_view_name"];
            $saveData["edit_view_name"] = $data["edit_view_name"];


            $saveData["parent_id"] = ($data["parent_id"] == "") ? 0 : $data["parent_id"];

            $saveData["position"] = $this->dataSource::count() + 1;

            //info($saveData);

            $arrSaveData = array("model" => $this->dataSource, "data" => $saveData);

            $created = $this->saveData($arrSaveData);


        } catch (\Exception $exception) {
            return array("created" => 0, "message" => $exception->getMessage());
        }

        return array("created" => $created);
    }


    /**
     * @return mixed
     */
    public function getFields()
    {

        $data = request()->all();
        $source = new $this->dataSource;

        return $source->getFieldsName($data["table"]);
    }

    /**
     * @param string $name
     */
    public function isControllerExists($name = "")
    {

        $request = request()->all();

        echo $this->isExists($request["name"], TRUE);

    }

    /**
     * @param $name
     * @param bool $isController
     * @return bool
     */
    private function isExists($name, $isController = TRUE)
    {

        //$namespace = app()->getNamespace();
        $namespace = config("hashtagcms.namespace");
        $namespace = (Str::endsWith($namespace, "\\")) ? substr($namespace, 0, strlen($namespace) - 1) : $namespace;

        if ($isController == TRUE) {

            $file_name = $namespace . '/Http/Controllers/Admin/' . ucfirst($name) . 'Controller.php';

        } else {

            $file_name = $namespace . '/Models/' . ucfirst($name) . '.php';
        }

        return file_exists(base_path($file_name));

    }


}
