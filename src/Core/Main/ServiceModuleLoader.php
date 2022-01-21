<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;

use Illuminate\Support\Facades\Http;

class ServiceModuleLoader extends Results implements ModuleLoaderImp
{

    protected array $result;

    function __construct(string $service_url=null, string $method_type=null, array $withData=array(), array $headers=array())
    {
        parent::__construct();
        if ($service_url != null) {
            $this->process($service_url, $method_type, $withData, $headers);
        }
    }

    /**
     * @param string|null $service_url
     * @param string|null $method_type
     * @param array $withData
     * @param array $headers
     * @return void
     */
    public function process(string $service_url=null, string $method_type=null, array $withData=array(), array $headers=array()):void
    {
        $data = array();
        if($service_url == "" || $service_url == null) {
            $this->setResult([]);
            return;
        }


        $urls = explode("?", $service_url);

        $arguments = array();

        //make sure if we are not passing data
        if(sizeof($urls)>1 && sizeof($withData) == 0) {
            //we have arguments too
            parse_str($urls[1], $arguments);
        }
        $url = $urls[0];
        $arguments = array_merge($arguments, $withData);

        try {
            $data = match (strtolower($method_type)) {
                "get" => Http::withHeaders($headers)->get($url, $arguments)->json(),
                "post" => Http::withHeaders($headers)->post($url, $arguments)->json(),
            };

        } catch (Exception $exception) {
            info("Error: getServiceModule ".$exception->getMessage());
            $this->setResult([]);
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
