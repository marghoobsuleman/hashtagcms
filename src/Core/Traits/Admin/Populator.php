<?php
namespace MarghoobSuleman\HashtagCms\Core\Traits\Admin;


trait Populator {


    /*
     * Popluate paginator data
     *
     * @param:
        $data["dataSource"] = $dataSource;
        $data["dataFields"] = $dataFields;
        $data["lang_id"] = 1;
        $data["actionFields"] = $actionFields;
     */
    public function pouplate($data=array()) {

        $source = $data['dataSource'];

        $sourceWith = $data['dataWith'];

        $where = $data['dataWhere'];

        $fields = $data['dataFields'];

        $actionFields = $data['actionFields'];

        $extraData = $data['extraData'];

        if($source!=null) {

            $data["paginator"] = $source::getData($sourceWith, array(), $where);

            /*echo "<pre>";
            print_r($data["paginator"]);
            echo "</pre>";*/

            if(sizeof($actionFields)>0 || sizeof($data["moreActionFields"]) >0) {
                array_push($fields, htcms_admin_config("action_field_title"));
            }

            $data["fieldsName"] = $fields;
            $data["actionFields"] = $actionFields;
            $data["extraData"] = $this->fetchExtraData($extraData);

        } else {

            $data["paginator"] = null;
            $data["fieldsName"] = null;
            $data["extraData"] = null;

        }


        $controller_name = request()->module_info->controller_name;
        //if module has a view name - load that - else try to find listing in module folder else common.listing
        $controller_view = request()->module_info->list_view_name;
        $listingView = ($controller_view == null || empty($controller_view)) ? $controller_name.'.listing'  : '.'.$controller_view;
        $listingView = str_replace("/", ".", $listingView);
        //check here if module has in own folder
        $viewName = [$listingView, "common.listing"];


        if(request()->get("asJson") == "true") {
            return $data;
        }

        return $this->viewNow($viewName, $data,false);

    }

    private function fetchExtraData($extra = NULL) {

        if($extra == NULL) {
            return NULL;
        }

        $extras = array();

        foreach ($extra as $key=>$extraData) {

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


        }
        return $extras;
    }

}
