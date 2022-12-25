<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\CmsModule;
use MarghoobSuleman\HashtagCms\Models\CmsPermission;
use MarghoobSuleman\HashtagCms\Models\Role;
use MarghoobSuleman\HashtagCms\Models\User;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;

class AuthorController extends BaseAdminController
{
    protected $dataFields = ['id', 'name', 'user_type', 'email', 'updated_at'];

    protected $dataSource = User::class;

    protected $dataWith = '';

    //protected $dataWhere = array(array("field"=>"user_type", "operator"=>"=", "value"=>"Staff"));

    protected $actionFields = array("edit", "delete"); //This is last column of the row

    protected $minResults = 1;

    protected $moreActionFields = array(
        array("label" => "Permission",
            "icon_css" => "fa fa-lock",
            "action" => "permission",
            "action_append_field" => "id")
    );

    protected $bindDataWithAddEdit = array("allRoles" => array("dataSource" => Role::class, "method" => "all"));


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        $rules = [
            'email' => 'required|email|max:255|unique:users',
            "name" => "required|max:255|string",
            "password" => "nullable|max:255|string",
            "facebook_user_id" => "nullable|max:255|string",
            "google_user_id" => "nullable|max:255|string",
            "remember_token" => "nullable|max:100|string"
        ];

        if ($request->input("id") > 0) {
            $rules['email'] = 'required|email|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = request()->all();

        $saveData["name"] = $data["name"];
        $saveData["email"] = $data["email"];
        $saveData["user_type"] = "Staff";

        $roles = $data["roles"]; //Save in user_role table
        $updateRoles = ($data["updateRoles"] == 1) ? TRUE : FALSE;

        if (!empty($data["password"])) {
            $saveData["password"] = User::makePassword($data["password"]);
        }

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

            $id = $savedData["id"];

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);

            $id = $savedData["id"];
        }


        //Insert/Update Roles
        if (!empty($roles) && $updateRoles == TRUE) {
            $user = User::find($id);
            $user->detachAllRoles(); //remove old roles

            //Get Roles
            $allRoles = Role::find($roles);
            $user->assignMultipleRole($allRoles); //Assign new roles

        }

        $viewData["id"] = $savedData["id"];
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];

        return htcms_admin_view("common.saveinfo", $viewData);
    }

    /**
     * Save Permission
     * @param $id
     * @return mixed
     */
    public function permission($user_id = 0)
    {

        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getReadError(), \request()->ajax());
        }

        if ($user_id == 0) {
            return array("error" => "Unable to read data for userId: $user_id");
        }

        $allModules = CmsModule::getAdminModules();


        $userWithModules = User::with('cmsmodules')->find($user_id);


        $viewData["results"] = array("id" => $user_id);
        $viewData["allModules"] = $allModules;
        $viewData["isSuperAdmin"] = $userWithModules->isSuperAdmin();
        $viewData["userModules"] = $userWithModules;
        $viewData["backURL"] = $this->getBackURL();
        $viewData["actionPerformed"] = "edit";



        return htcms_admin_view("author.permission", $viewData);

    }

    //working here

    /**
     * Save Module Permission
     * @return mixed
     */
    public function saveModulePermissions()
    {

        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getReadError());
        }
        try {
            $data = request()->all();

            $cmsModuleData = $data['cmsModuleData'];

            $userId = $data['userId'];

            $saveData = array();

            $savedData = array();

            foreach ($cmsModuleData as $cmsModule) {
                $isReadOnly = (isset($cmsModule["readonly"]) && $cmsModule["readonly"] === true) ? 1 : 0;
                $selected = $cmsModule["selected"] ?? 0;
                if ($selected) {
                    $saveData[] = array("module_id" => $cmsModule["id"], "user_id" => $userId, "readonly" => $isReadOnly);
                }

                if (!empty($cmsModule["child"])) {
                    foreach ($cmsModule["child"] as $child) {
                        $isReadOnly = (isset($child["readonly"]) && $child["readonly"] === true) ? 1 : 0;
                        $selected = $child["selected"] ?? 0;
                        if ($selected) {
                            $saveData[] = array("module_id" => $child["id"], "user_id" => $userId, "readonly" => $isReadOnly);
                        }
                    }
                }
            }

            //Delete old
            CmsPermission::detachOldModules($userId);
            $arrSaveData = array("model" => CmsPermission::class, "data" => $saveData);

            $savedData = $this->saveData($arrSaveData);

        } catch (Exception $exception) {
            $savedData = array("message" => $exception->getMessage());
        }


        return $savedData;

    }




}
