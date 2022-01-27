<?php

namespace MarghoobSuleman\HashtagCms\Core\Traits\Admin;

use MarghoobSuleman\HashtagCms\Models\Lang;
use MarghoobSuleman\HashtagCms\Models\Site;

trait Common {

    /**
     * Get Supported Site Lang
     * @param null $site_id
     * @return mixed
     */
    public function getSupportedSiteLang($site_id = NULL) {

        $site_id = ($site_id == NULL) ? htcms_get_siteId_for_admin() : $site_id;
        return Site::with('language')->find($site_id)->language;

    }

    public function getSupportedSitePlatform($site_id = NULL) {

        $site_id = ($site_id == NULL) ? htcms_get_siteId_for_admin() : $site_id;
        $site = Site::with('platform')->where("id", "=", $site_id)->get();
        return $site[0]->platform;

    }

    /**
     * Get all language
     * @return mixed
     */
    public function getAvailableLang() {
        return Lang::all();
    }


    /**
     * Return an array from pivot field.
     *
     * @param $pivotData
     * @param $column
     * @return array
     */
    public static function pivotToArray($pivotData, $column="") {

        $data_arr = array();

        foreach ($pivotData as $key=>$value) {
            if($column != "") {
                foreach($value["pivot"] as $innerKey=>$innerValue) {
                    if($innerKey==$column) {
                        $data_arr[] = $innerValue;
                    }
                }
            } else {
                $data_arr[] = $value["pivot"];
            }

        }
        //return zero element if there is only one element in an array - in case of all fields
        if($column == "") {
            $data_arr = (sizeof($data_arr) == 1) ? $data_arr[0] : $data_arr;
        }
        return $data_arr;
    }

    /**
     * Desc: Is Associative array
     * @param array $arr
     * @return bool
     */
    private function is_array_associative(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }


}
