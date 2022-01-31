<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;


use Mockery\Exception;

class QueryServiceModuleLoader extends Results implements ModuleLoaderImp
{

    protected array $result;

    function __construct(string $query, string $serviceUrl, string $query_as, string $method_type, string $database=null)
    {
        parent::__construct();
        if ($query != null) {
            $this->process($query, $serviceUrl, $query_as, $method_type, $database);
        }
    }

    /**
     * @param string $query
     * @param string $serviceUrl
     * @param string $query_as
     * @param string $method_type
     * @return void
     */
    public function process(string $query, string $serviceUrl, string $query_as, string $method_type, string $database=null):void
    {

        $data = array();

        $qm = new QueryModuleLoader($query, $database);
        $data["queryData"] = $qm->getResult();

        if($query_as == "data" || $query_as == "") {

            //we should return the data
            $sm = new ServiceModuleLoader($serviceUrl, $method_type);
            $data["serviceData"] = $sm->getResult();

        } else {
            try {
                $arguments = json_decode(json_encode($data["queryData"]), true)[0]; //kind fo toArray()
                $sm = new ServiceModuleLoader($serviceUrl, $method_type, $arguments);
                $data["serviceData"] = $sm->getResult();
            } catch (Exception $exception) {

                $data["serviceData"] = null;
            }


        }

        $this->setResult($data);
    }

    /**
     * @return array
     */
    public function getResult():array
    {
        return $this->result;
    }


    /**
     * @param array $data
     * @return void
     */
    public function setResult(array $data):void
    {
        $this->result = collect($data)->all();
    }

}
