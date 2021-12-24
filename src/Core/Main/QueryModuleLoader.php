<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;


class QueryModuleLoader extends Results implements ModuleLoaderImp
{

    protected array $result;

    function __construct(string $query=null)
    {
        parent::__construct();
        if ($query != null) {
            $this->process($query);
        }
    }

    /**
     * @param string $query
     * @return void
     */
    public function process(string $query):void
    {
        $data = $this->dbSelect($query);
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
