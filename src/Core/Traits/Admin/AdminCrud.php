<?php

namespace MarghoobSuleman\HashtagCms\Core\Traits\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate as GateFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use MarghoobSuleman\HashtagCms\Models\Permission;
use MarghoobSuleman\HashtagCms\Models\QueryLogger;
use MarghoobSuleman\HashtagCms\Models\Site;

use MarghoobSuleman\HashtagCms\Core\Helpers\Message;

trait AdminCrud {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($more=null)
    {
        if(!$this->checkPolicy('read')) {
            return htcms_admin_view("common.error", Message::getReadError());
        }

        $data = $this->getSegregatedData();

        if($more!=null) {
            $data = array_merge($data, $more);
        }

        //This is in Trait
        return $this->pouplate($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->edit();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*public function store()
    {
        This has to be in controller because of Validator
    }*/

    /**
     * Display the specified resource. Edit Alias
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id=0, $param1=0)
    {

        if(!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }

        //Check if has pre edit
        if(method_exists($this, 'preEdit')) {
            $this->preEdit();
        }

        $dataSource = $this->getDataSource();
        $dataWith = $this->getDataWith();

        $data['results'] =  array();

        $data["actionPerformed"] = ($id>0) ? "edit" : "add";

        $data["backURL"] = $this->getBackURL(FALSE, $id);

        if($id>0) {

            $data['results'] =  $dataSource::getById($id, $dataWith, $param1);
            $data["backURL"] = $this->getBackURL(TRUE, $id);
        }

        $data['user_rights'] = $this->getUserRights();

        //In case if you want any extra;
        $extraData = $this->getExtraDataForEdit();

        if($extraData != NULL) {
            $data = array_merge($data, $extraData);
        }


        /*echo "<pre>";
         print_r($data);
         echo "</pre>";*/
        $controller_name = request()->module_info->controller_name;

        $controller_view = request()->module_info->edit_view_name;
        $editView = ($controller_view == null || empty($controller_view)) ? $controller_name.'.addedit'  : '.'.$controller_view;

        $editView = str_replace("/", ".", $editView);

        //return $data;
        return htcms_admin_view($editView, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if(!$this->checkPolicy('delete')) {
            return json_encode(array("id"=>$id, "success"=>0, "message"=>Message::getDeleteError()));
        }

        //DB::enableQueryLog();

        $dataSource = $this->getDataSource();
        $source = new $dataSource();



        $isDeleted = $source->destroy($id);
        $array = array("id"=>$id, "success"=>$isDeleted, "source"=>$source);

        //$query = DB::getQueryLog();

        //self::log('editStart', json_encode($query), json_encode($data), $id);

        return json_encode($array);
    }



    /*** HELPER METHODS **/

    /**
     * Save Data with Lang
     *
     * @param array $saveData
     * @param array $langData
     * @param null $where
     * @return mixed
     */

    protected function saveDataWithLang($saveData=array(), $langData=array(), $where=NULL) {

        $data["saveData"] = $saveData;
        $data["langData"] = $langData;

        return $this->saveAllData($data, $where);
    }

    /**
     * Save Data with Lang and Site
     * @param array $saveData
     * @param array $langData
     * @param array $siteData
     * @param null $where
     * @return mixed
     */
    //working
    protected function saveDataWithLangAndSite($saveData=array(), $langData=array(), $siteData=array(), $where=NULL) {

        $data["saveData"] = $saveData;
        $data["langData"] = $langData;

        $data["siteData"] = $siteData;

        return $this->saveAllData($data, $where);
    }

    protected function saveDataWithLangAndTenant($saveData=array(), $langData=array(), $tenantData=array(), $where=NULL) {

        $data["saveData"] = $saveData;
        $data["langData"] = $langData;

        $data["tenantData"] = $tenantData;

        return $this->saveAllData($data, $where);
    }

    /**
     * Save Data
     * @param array $saveData
     * @param null $where
     * @return mixed
     */
    protected function saveData($saveData=array(), $where=NULL) {

        $data["saveData"] = $saveData;

        return $this->saveAllData($data, $where);

    }

    /**
     * Save All Data
     * @param $data - mainData, langData, siteData,
     * @param null $where
     * @return mixed
     */
    private function saveAllData($data, $where=NULL) {

        //Better to be safe
        if(!$this->checkPolicy('edit')) {
            return Message::getWriteError();
        }

        QueryLogger::enableQueryLog();

        $savedDataModel =  $data["saveData"]["model"];
        $savedData =  $data["saveData"]["data"];


        $langData = NULL;
        $siteData = NULL;

        //Lang Data
        if(isset($data["langData"])) {
            $langData = $data["langData"]["data"];
            $site_id = htcms_get_siteId_for_admin();
            if(isset($data["siteData"])) {
                $site_id = $data["siteData"]["site_id"] ??  htcms_get_siteId_for_admin();
            }
            if(isset($data["tenantData"])) {
                $site_id = $data["tenantData"]["site_id"] ??  htcms_get_siteId_for_admin();
            }
            $supportedSiteLangs = $this->getSupportedSiteLang($site_id); //This is in Common Trait

        }

        $rData = array();

        //Save | Insert
        if($where==NULL || $where<=0) {

            //Save Main Model
            $mainModel = new $savedDataModel();

            //make key val;
            $fieldKeyVal = array();
            foreach ($savedData as $key=>$val) {
                //$mainModel->{$key} = $val;
                $fieldKeyVal[$key] = $val;
            }

            //Save main model
            $rData["isSaved"] = $mainModel->insert($fieldKeyVal);
            //$rData["isSaved"] = $mainModel->save();

            try {
                //Sometime there is no id field
                $rData["id"] = DB::getPdo()->lastInsertId();

            } catch (Exception $e) {
                $rData["id"] = NULL;
            }

            $rData["source"] = $mainModel;



            if(isset($data["langData"]) || isset($data["siteData"]) || isset($data["tenantData"])) {
                $mainModel = $mainModel->find($rData["id"]);
            }

            //Save Language Model
            if($langData!=NULL) {
               // dd($supportedSiteLangs);
                $langDatas = array();
                foreach ($supportedSiteLangs as $key=>$siteLang) {

                    $langData["lang_id"] = $siteLang["id"];
                    //info(json_encode($siteLang));
                    //Prepair for buld insert
                    $langDatas[] = $langData;
                }
                //info(json_encode($langDatas));

                $rData["isSavedLang"] = $mainModel->lang()->createMany($langDatas);
            }

            //Site Data
            try {
                if(isset($data["siteData"])) {
                    //Model must have belongsToMany relation with 'site'
                    $siteData = $data["siteData"]["data"];
                    $siteInfo = Site::find($siteData["site_id"]);
                    unset($siteData["site_id"]);
                    $mainModel->site()->attach($siteInfo, $siteData);
                }
            } catch (\Exception $e) {
                info($e->getMessage(), "Error: ");
            }


            //Tenant Data
            if(isset($data["tenantData"])) {
                //Model must have belongsToMany relation with 'tenant'
                $tenantData = $data["tenantData"]["data"];
                $supportedSiteTenant = $this->getSupportedSiteTenant($tenantData['site_id']);//tenant data must have a site_id

                //add in supported tenant
                foreach ($supportedSiteTenant as $key=>$tenant) {
                    $tenantData["tenant_id"] = $tenant["id"];
                    $mainModel->tenant()->attach($tenant, $tenantData);
                }

            }
            $queryLog = QueryLogger::getQueryLog();
            $actionLog = "insert";

        } else {

            //Update
            $mainModel = new $savedDataModel();

            //lang data
            if($langData!=NULL) {

                $mainModel = $mainModel->with('lang')->find($where);

            } else {

                $mainModel = $mainModel->find($where);
            }

            $rData["isSaved"] = $mainModel->update($savedData);

            $rData["id"] = $where;

            $rData["source"] = $mainModel;

            if($langData != NULL) {
                //info("lang_id: ".$langData["lang_id"]);
                $langData["lang_id"] = (isset($langData["lang_id"])) ? $langData["lang_id"] : htcms_get_language_id_for_admin();
                $rData["isSavedLang"] = $mainModel->lang()->update($langData);
            }

            if(isset($data["siteData"])) {
                //Model must have belongsToMany relation with 'site'
                $siteData = $data["siteData"]["data"];
                $mainModel->site()->updateExistingPivot($siteData["site_id"], $siteData);
            }

            if(isset($data["tenantData"])) {
                //Model must have belongsToMany relation with 'tenant'
                $tenantData = $data["tenantData"]["data"];
                //info(json_encode($tenantData));
                $mainModel->tenant()->updateExistingPivot($tenantData["tenant_id"], $tenantData);
            }

            $queryLog = QueryLogger::getQueryLog();
            $actionLog = "update";
        }


        //Logging
        try{

            QueryLogger::log($actionLog, $queryLog, $data, $rData['id'] ?? 0);

        } catch (\Exception $exception) {

            info($exception->getMessage());

        }

        return $rData;
    }


    /********* EXTRA ******************/
    /**
     * Search the specified resource.
     *
     * @param  \MarghoobSuleman\HashtagCms\Models\SourceModel
     * @return \Illuminate\Http\Response
     */

    public function search() {

        if(!$this->checkPolicy('read')) {
            return htcms_admin_view("common.error", Message::getReadError());
        }

        $data = $this->getSegregatedData();

        $collection = collect(request()->all());


        $filtered = $collection->except(['page']);

        $data["searchId"] = $filtered->all();


        //This is in Trait
        return $this->searchData($data);

    }


    /**
     * @param int $id
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish($id=0, $status=0) {

        if(!$this->checkPolicy('publish')) {
            return Message::getWriteError();
        }

        QueryLogger::enableQueryLog();

        $status = ($status==0) ? 1 : 0;
        $where = $id;
        $saveData["publish_status"] =  $status;

        $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);

        $savedData = $this->saveData($arrSaveData, $where);


        $rData = [
            'id' => $id,
            'status' => $status,
            'meta' => $savedData
        ];

        //Logging
        try{
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('publish', $queryLog, $rData, $where);

        } catch (\Exception $exception) {

            info($exception->getMessage());

        }

        return response()->json($rData);

    }


    /**
     * @param string $table
     * @param array $data
     * @return bool
     */
    public function rawInsert($table="", $data=array()) {

        QueryLogger::enableQueryLog();

        DB::beginTransaction();

        $status = 0;

        try {

            $status = DB::table($table)->insert($data);

        } catch (\Exception $exception) {

            DB::rollBack();
            QueryLogger::log('rawInsert', "raw insert failed");

            return $status;
        }

        DB::commit();

        //Logging
        try{

            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('rawInsert', $queryLog, $data, $status);

        } catch (\Exception $exception) {

            info($exception->getMessage());

        }

        return $status;
    }

    /**
     * @param string $table
     * @param array $where
     * @return int
     */
    public function rawDelete($table="", $where=array()) {

        QueryLogger::enableQueryLog();

        DB::beginTransaction();

        $status = 0;

        try {

            $status = DB::table($table)->where($where)->delete();

        } catch (\Exception $exception) {

            DB::rollBack();
            QueryLogger::log('rawDelete', "raw delete failed");

            return $status;
        }

        DB::commit();

        //Logging
        try{

            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('rawDelete', $queryLog, $table, $where);

        } catch (\Exception $exception) {

            info($exception->getMessage());

        }

        return $status;
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return int
     */
    public function rawUpdate($table="", $data=array(), $where=array()) {

        QueryLogger::enableQueryLog();

        $update = DB::table($table)
            ->where($where)
            ->update($data);

        //Logging
        try{

            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('rawUpdate', $queryLog, $data, $where);

        } catch (\Exception $exception) {

            info($exception->getMessage());

        }

        return $update;
    }



    /******** PRIVATE METHODS ***************/

    /**
     * Get All vars for listing etc
     * @return mixed
     */

    private function getSegregatedData() {

        $data["actionFields"] = $this->getFilteredActions();
        $data["moreActionFields"] = $this->getMoreActionFields();
        $data["dataSource"] = $this->getDataSource();
        $data["dataWith"] = $this->getDataWith();
        $data["dataFields"] = $this->getDataFields();
        $data["dataWhere"] = $this->getDataWhere();
        $data["supportedLangs"] = $this->getSupportedSiteLang(htcms_get_siteId_for_admin());
        $data["hasLangMethod"] = (method_exists($data["dataSource"], 'lang')) ? "true" : "false";
        $data['user_rights'] = $this->getUserRights();
        $data['extraData'] = $this->getExtraDataForListing();
        $data['moreActionBarItems'] = $this->getMoreActionBarItems();
        $data['minResults'] = (isset($this->minResults)) ? $this->minResults : -1;

        return $data;
    }

    /**
     * Get Filtered Actions
     *
     * @return array
     */

    private function getFilteredActions() {


        $action = array();

        if(isset($this->actionFields)) {
            //Check in gate
            foreach($this->actionFields as $field) {
                if(GateFacade::allows($field)) {
                    $action[] = $field;
                }
            }
        }

        return $action;
    }


    /**
     * Get Data Fields
     *
     * @return array
     *
     */
    private function getDataFields() {

        $arr = isset($this->dataFields) ? $this->dataFields : [];

        if(isset($this->dataFields)) {

            if(is_array($this->dataFields) && Str::contains(join("", Arr::flatten($this->dataFields)), " as ")==1) {

                $arr = array();

                foreach($this->dataFields as $field) {

                    if(is_string($field) && Str::contains($field, " as ")) {

                        $c = explode(" as ", $field);
                        $arr[] = array("key"=>$c[0], "label"=>$c[1]);

                    } else {

                        $arr[] = $field;

                    }
                }
            } else if($this->dataFields==trim("*")) {
                $dataSource = $this->getDataSource();

                if($dataSource!=NULL) {
                    $model = new $dataSource;
                    $this->dataFields = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
                    $arr = $this->dataFields;
                }
            }

        }
        //return default
        return $arr;

    }


    private function getMoreActionFields() {
        return $this->moreActionFields ?? array();
    }


    /**
     * Get Data "with"
     * @return string
     */
    private function getDataWith() {
        return (isset($this->dataWith)) ? $this->dataWith : '';
    }

    /**
     * Get Data "with"
     * @return string
     */
    private function getDataWhere() {
        return (isset($this->dataWhere)) ? $this->dataWhere : array();
    }


    /**
     * Get extra data for listing
     * @return null
     */
    private function getExtraDataForListing() {
        return (isset($this->bindDataWithListing)) ? $this->bindDataWithListing : NULL;
    }

    /**
     * Action bar items
     * @return array
     */
    private function getMoreActionBarItems() {
        return (isset($this->moreActionBarItems)) ? $this->moreActionBarItems : array();
    }

    /**
     *
     * Get Data Source
     * @return array
     *
     */
    private function getDataSource() {
        return (isset($this->dataSource)) ? $this->dataSource : NULL;
    }


    /**
     * Get Extra Data - if you needed at the time of add/edit
     * @return array|null
     * @throws \ReflectionException
     */
    protected function getExtraDataForEdit($bindData=NULl, $useBoth=FALSE) {

        if(isset($this->bindDataWithAddEdit) || $bindData!=NULL) {
            $extras = array();
            $useData = ($bindData==NULL) ? $this->bindDataWithAddEdit : $bindData;

            if($useBoth==TRUE && isset($this->bindDataWithAddEdit)) {
                $useData = array_merge($this->bindDataWithAddEdit, $bindData);
            }

            foreach ($useData as $key=>$extraData) {
                if(isset($extraData["dataSource"])) {
                    $source = $extraData["dataSource"];
                    $method = $extraData["method"];
                    $params = isset($extraData["params"]) ?
                        ((is_string($extraData["params"]) ? array($extraData["params"]) : $extraData["params"]))
                        : array();

                    $checker = new \ReflectionMethod($source,$method);
                    if($checker->isStatic()) {
                        $extras[$key] = call_user_func_array(array($source, $method), $params);
                    } else {
                        $source_obj = new $source;
                        $extras[$key] = $source_obj->{$method}($params);
                    }
                } else {
                    $extras[$key] = $extraData;
                }




            }
            return $extras;
        }

        return NULL;
    }

    /**
     * GetBackURL
     * @param $isEdit
     * @param int $id
     * @return array|mixed|string
     */
    protected function getBackURL($isEdit=FALSE, $id=0):string {

        if($id==0) {

            $backURL = htcms_admin_path(request()->module_info->controller_name);

        } else {

            $backURL = url()->previous();

            $backURL_arr = explode("?",$backURL);

            $backURL_Base = $backURL_arr[0];

            parse_str( parse_url( html_entity_decode($backURL), PHP_URL_QUERY), $queryParams_arr);

            $queryParams_arr["id"] = $id;

            $params = http_build_query($queryParams_arr);

            $separator = "?";
            $backURL = $backURL_Base.$separator.$params;

        }

        //if editing directory
        if(url()->current() == url()->previous()) {
            $backURL = htcms_admin_path(request()->module_info->controller_name);
        }


        return $backURL;
    }

    /**
     * Get User Rights
     * @return array
     */
    protected function getUserRights() {

        return (request()->user()->isSuperAdmin()==1) ? Arr::flatten(Permission::all("name")->toArray()) : request()->user()->rights();
    }


}
