<?php

namespace MarghoobSuleman\HashtagCms\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Lang extends AdminBaseModel
{

    protected $guarded = array();

    /**
     * With site
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function site() {
        return $this->hasMany(LangSite::class);
    }



    /**
     * Insert Lang in all tables
     * @param $ids
     */
    public static function insertLangInAllTables($id=NUll) {

        if($id == NULL) {
            return false;
        }

        $newLangId = $id;

        $tables = DB::select('SHOW TABLES');

        $lang = Lang::first(); // @todo: should fetch default site language

        foreach($tables as $key=>$tables) {

            foreach ($tables as $tKey => $table_name) {

                if(Str::endsWith($table_name, '_langs')) {

                    $where = array(array("lang_id", "=", $lang->id)); //get first lang

                    //fetched default lang
                    $fetchedData = DB::table($table_name)->where($where)->get()->toArray();

                    $toBeInsertedData = [];

                    if(sizeof($fetchedData) > 0) {
                        //$fetchedData = json_decode(json_encode($fetchedData), true);
                        foreach ($fetchedData as $currentData) {
                            $currentData->lang_id = $newLangId;
                            // in case there is any primary key in lang table
                            if(isset($currentData->id)) {
                                unset($currentData->id); //remove primary key
                            }
                            $toBeInsertedData[] = (array)$currentData;
                        }

                        if(sizeof($toBeInsertedData)>0) {

                            DB::table($table_name)->insert($toBeInsertedData);

                        }

                    }


                }

            }
        }

    }


    public function copyLangData($sourceLangId=null, $targetLangId=null, $tables=array()) {

        $data = [];
        foreach ($tables as $table) {
            $rows = DB::select("select * from $table where lang_id=:lang_id", array("lang_id"=>$sourceLangId));
            $targetRows = DB::select("select * from $table where lang_id=:lang_id", array("lang_id"=>$targetLangId));
            if($targetRows->count() == 0) {
                //manipulate data
                foreach ($rows as $row) {
                    $row->lang_id = $targetLangId;
                    $row->created_at = date("Y-m-d H:is");
                    $row->updated_at = date("Y-m-d H:is");
                }

                $status = $this->rawInsert($table, $rows);
                $msg = ($status == 0) ? "There is some error while copying content." : "$table has been copied.";
            } else {
                $status = 0;
                $msg = "Target language is already there. Unable to copy";
            }
            $data[] = array("table"=>$table, "status"=>$status, "message"=>$msg);

        }
    }


    /**
     * Get all language tables
     * @return array
     */
    public static function getAllLangTables() {
            $tables = DB::select('SHOW TABLES');
            $arr = [];
            foreach($tables as $key=>$table) {
                foreach ($table as $key => $value) {
                    if(!Str::endsWith($value, '_langs')) {
                        $table = $value;
                        $langTable = Str::singular($value)."_langs";
                        if(Schema::hasTable($langTable)) {
                            $arr[] = array("name" => $langTable, "baseTable"=>$table);
                        }
                    }
                }
            }
            return $arr;
    }

}
