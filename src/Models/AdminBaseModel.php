<?php
namespace MarghoobSuleman\HashtagCms\Models;

/***
 * AdminBaseModel
 * this is base admin model
 *
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



abstract class AdminBaseModel extends Model
{


    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('hashtagcmsadmin.cmsInfo.records_per_page');
    }

    /**
     * Get Data from current model
     *
     * @param string $with
     * @param array $searchParams
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getData($with='', $searchParams=array(), $where=array()) {

        //DB::enableQueryLog();
        $obj = ($with!='') ? static::with($with) : new static;

        //add where condition
        if(sizeof($searchParams)>0) {

            foreach ($searchParams as $key=>$searchParam) {

                switch ($key) {
                    case "where":
                        $obj = $obj->where($searchParam[0], $searchParam[1], $searchParam[2]);
                        break;
                }

            }
        }

        if(sizeof($where) > 0) {

            //array("field"=>"", "operator"=>"", "value"=>"")
            foreach ($where as $index=>$item) {
                $obj = $obj->where($item["field"], $item["operator"], $item["value"]);
            }
        }


        //Order by id desc
        $obj = $obj->orderBy($obj->getModel()->getKeyName(), "DESC");

        $res = $obj->paginate(htcms_admin_config('records_per_page'));

        //dd(DB::getQueryLog());
        return $res;
    }


    /**
     * @param string $with
     * @param null $field
     * @param null $opr
     * @param null $val
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function searchData($with='', $field=NULL, $opr=NULL, $val=NULL, $where=array()) {

        $arr = NULL;
        //echo "$field, $opr, $val";

        if($field!==NULL && $opr!=NULL && $val!=NULL) {

            switch ($opr) {
                case "like%":
                    $val = $val."%";
                    break;
                case "%like%":
                    $val = "%".$val."%";
                    break;
            }

            $opr = (Str::contains($opr, "like")==1) ? "like" : $opr;

        }

        if(Str::contains($field, ".")==1) {

            $field = explode(".", $field);
            $relationWhere = $field[0];
            $field = $field[1];

            //in case $source with (relation) is not defined but you have this in relationship
            if(empty($relationWhere)) {
                $with = $relationWhere;
            }

            $data = self::with($with)->whereHas($relationWhere, function ($query) use ($field, $opr, $val) {

                $query->where($field, $opr, $val);

            })->paginate(htcms_admin_config('records_per_page'));


            return $data;


        } else {

                $arr = array("where"=>array($field, $opr, $val));
        }

        return self::getData($with, $arr);
    }

    /**
     * Find By Id
     *
     * @param int $id
     * @param string $with
     * @return array
     */
    public static function getById($id=0, $with='') {

        QueryLogger::enableQueryLog();

        if($with != '') {
            $with = (is_array($with)) ? $with : array($with);
        }

        $obj = ($with!='') ? static::withoutGlobalScopes()->with($with) : new static;

        $data = $obj->findOrFail($id)->toArray();

        $queryLog = QueryLogger::getQueryLog();

        try{

            QueryLogger::log('editStart', $queryLog, $data, $id);

        } catch (\Exception $exception) {

            info($exception->getMessage());

        }


        return $data;

    }


    /**
     * Get data as compatible to render combobox
     * @param array $fileds
     * @param string $loadWith
     * @return AdminBaseModel[]|\Illuminate\Database\Eloquent\Collection
     */
    public function combo($fileds=array(), $loadWith='lang') {

        if(method_exists ( $this, 'lang' )) {
            if(sizeof($fileds)>0) {
                return self::all($fileds)->load($loadWith);
            } else {
                return self::all()->load($loadWith);
            }
        } else {
            if(sizeof($fileds)>0) {
                return self::all($fileds);
            } else {
                return self::all();
            }
        }

    }


    /**
     * Get Enum Values from any table
     * @param string $table
     * @param string $field
     * @return array
     */
    public static function getEnumValues($table='', $field='') {

        $enum = array();
        if($table != '' && $field != '') {
            $type = DB::select(DB::raw("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'"));
            if(sizeof($type) > 0 ){
                $type = $type[0]->Type;
                preg_match('/^enum\((.*)\)$/', $type, $matches);
                foreach( explode(',', $matches[1]) as $value ) {
                    $enum[] = trim( $value, "'" );
                }
                return $enum;
            }
        }

        return $enum;

    }



}
