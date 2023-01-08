<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;

use Illuminate\Support\Facades\DB;

/**
 * Using session for now
 */
class Results
{

    public function __construct()
    {

    }


    /**
     * Parse query and get the results
     * @param string $query
     * @param array $byParams
     * @return array|null (optional)
     */
    public function dbSelect(string $query, array $byParams=array(), string $database=null): ?array
    {
        //$queryParams = $this->findAndReplaceGlobalIds($query);
        $queryParams = (sizeof($byParams)==0) ? $this->findAndReplaceGlobalIds($query) : $byParams;

        /*info("======================== query ============= ");
        info(json_encode($query));
        info(json_encode($queryParams));*/
        try {
            if($database === null) {
                if(sizeof($queryParams) > 0) {
                    return DB::select($query, $queryParams);
                }
                return DB::select($query);
            } else {
                if(sizeof($queryParams) > 0) {
                    return DB::connection($database)->select($query, $queryParams);
                }
                return DB::connection($database)->select($query);
            }


        } catch (\Exception $e) {
            info("dbSelect: There is an error: ".$e->getMessage());
            return array();
        }

    }

    /**
     * Parse query and get the results
     * @param string $query
     * @param array $byParams
     * @return array|null (optional)
     */
    public function dbSelectOne(string $query, array $byParams=array(), string $database=null):?array {

        $queryParams = (sizeof($byParams)==0) ? $this->findAndReplaceGlobalIds($query) : $byParams;

        //info(json_encode($queryParams));;

        /*info("======================== query ============= ");
        info(json_encode($query));
        info(json_encode($queryParams));*/

        try {

            if($database === null) {

                if(sizeof($queryParams) > 0) {
                    $data =  DB::selectOne($query, $queryParams);
                } else {
                    $data = DB::selectOne($query);
                }

            } else {
                if(sizeof($queryParams) > 0) {
                    return DB::connection($database)->selectOne($query, $queryParams);
                }
                return DB::connection($database)->selectOne($query);
            }

            return  ($data !== null) ? (array)$data : array();

        } catch (Exception $e) {
            info("dbSelectOne: There is an error: ".$e->getMessage());
            return array();
        }
    }

    /**
     * Find and Replace for making query
     * :site_id will be array("site_id"=>1)
     * @param string $str
     * @return array
     */
    private function findAndReplaceGlobalIds(string $str): array
    {
        $infoLoader = app()->HashtagCms->infoLoader();
        $subject = $str;
        $pattern = "/:\w+/i";
        preg_match_all($pattern, $subject, $matches); //PREG_OFFSET_CAPTURE
        $arr = array();
        $matches = $matches[0];
        //info("matches: ". json_encode($matches));
        foreach ($matches as $key=>$val) {
            $queryKey = $val;
            $gKey = $infoLoader->getContextVars($queryKey);
            //info("gKey: queryKey: $queryKey");
            if(isset($gKey) && $gKey != null) {
                $arr[$gKey["key"]] = $gKey["value"];
            }
        }
        return $arr;
    }

}
