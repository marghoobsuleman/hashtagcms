<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;

use Illuminate\Support\Facades\Http;

class UrlServiceModuleLoader extends Results implements ModuleLoaderServiceImp
{

    protected array $result;

    /**
     * @param string|null $service_url
     * @param string|null $method_type
     * @param string|null $data_key_map
     */
    function __construct(string $service_url=null, string $method_type=null, array $withData=array(), string $data_key_map=null, array $headers=array())
    {
        parent::__construct();
        if ($service_url != null) {
            $this->process($service_url, $method_type, $withData, $data_key_map, $headers);
        }
    }

    /**
     * @param string|null $service_url
     * @param string|null $method_type
     * @param string|null $data_key_map
     * @return void
     */
    public function process(string $service_url=null, string $method_type=null, array $withData=array(), string $data_key_map=null,  array $headers=array()):void
    {

        if($service_url == "" || $service_url == null) {
            info("Service url is missing");
            $this->setResult([]);
        }
        $urls = explode("?", $service_url);

        $arguments = array();

        //make sure if we are not passing data
        if(sizeof($urls)>1) {
            //we have arguments too
            parse_str($urls[1], $arguments);
        }

        $url = $urls[0];
        $withData = array_merge($arguments, $withData);

        $arguments = $this->makeQueryParams($data_key_map);
        $arguments = array_merge($withData, $arguments);

        if (isset($arguments['resultType'])) {
            $headers['Content-Type'] = $arguments['resultType'];
        }

        $ml = new ServiceModuleLoader($url, $method_type, $arguments, $headers);
        $this->setResult($ml->getResult());

    }

    /**
     * @return array
     */
    public function getResult():mixed
    {
        return $this->result;
    }


    /**
     * @param array $data
     * @return void
     */
    public function setResult(mixed $data):void
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
