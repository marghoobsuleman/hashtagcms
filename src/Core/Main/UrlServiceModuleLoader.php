<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;

use Illuminate\Support\Facades\Http;

class UrlServiceModuleLoader extends Results implements ModuleLoaderImp
{

    protected array $result;

    /**
     * @param string|null $service_url
     * @param string|null $method_type
     * @param string|null $data_key_map
     */
    function __construct(string $service_url=null, string $method_type=null, string $data_key_map=null)
    {
        parent::__construct();
        if ($service_url != null) {
            $this->process($service_url, $method_type, $data_key_map);
        }
    }

    /**
     * @param string|null $service_url
     * @param string|null $method_type
     * @param string|null $data_key_map
     * @return void
     */
    public function process(string $service_url=null, string $method_type=null, string $data_key_map=null):void
    {

        if($service_url == "" || $service_url == null) {
            info("Service url is missing");
            $this->setResult([]);
        }
        $urls = explode("?", $service_url);
        $url = $urls[0];
        $arguments = $this->makeQueryParams($data_key_map);
        $ml = new ServiceModuleLoader($url, $method_type, $arguments);
        $this->setResult($ml->getResult());

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

    /**
     * @param string $url
     * @param string $data_key_map
     * @return array
     */
    private function makeQueryParams(string $data_key_map):array {
        $dataKeyMap = explode(",", $data_key_map);
        $arr = array();
        foreach ($dataKeyMap as $key=>$val) {
            $arr[$val] = request()->input($val);
        }
        return $arr;

    }

}
