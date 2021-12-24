<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;


class QueryServiceModuleLoader extends Results implements ModuleLoaderImp
{

    protected array $result;

    function __construct(string $query, string $serviceUrl, string $query_as, string $method_type)
    {
        parent::__construct();
        if ($query != null) {
            $this->process($query, $serviceUrl, $query_as, $method_type);
        }
    }

    /**
     * @param string $query
     * @param string $serviceUrl
     * @param string $query_as
     * @param string $method_type
     * @return void
     */
    public function process(string $query, string $serviceUrl, string $query_as, string $method_type):void
    {

        $data = array();

        if($query_as == "data" || $query_as == "") {
            //we should retrun the data
            $sm = new ServiceModuleLoader($serviceUrl, $method_type);
            $qm = new QueryModuleLoader($query);
            $data["serviceData"] = $sm->getResult();
            $data["queryData"] = $qm->getResult();

        } else {
            $qm = new QueryModuleLoader($query);
            $data2 = $qm->getResult();
            $arguments = json_decode(json_encode($data2), true)[0]; //kind fo toArray()

            $data["queryData"] = $data2;
            $sm = new ServiceModuleLoader($serviceUrl, $method_type, $arguments);
            $data["serviceData"] = $sm->getResult();

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
