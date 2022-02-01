<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ServiceModuleLoader extends Results implements ModuleLoaderServiceImp
{

    protected array $result = array();

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
        if(sizeof($urls)>1) {
            //we have arguments too
            parse_str($urls[1], $arguments);
        }

        $url = $urls[0];
        $arguments = array_merge($arguments, $withData);
        $contentTypes = array('text/css', 'text/csv', 'text/html', 'text/plain', 'text/xml');

        $contentType = "json";

        //if in params
        if (isset($arguments['resultType']) && in_array($arguments['resultType'], $contentTypes)) {
            $headers['Content-Type'] = $arguments['resultType'];
            $contentType = "text";
        }
        //if in headers - it can merge with above one. but don't want to.
        if (isset($headers['Content-Type']) && in_array($headers['Content-Type'], $contentTypes)) {
            $contentType = "text";
        }

        try {

            switch (strtolower($method_type)) {
                case "get":
                    if ($contentType === "text") {
                        $data = Http::withHeaders($headers)->get($url, $arguments)->body();
                    } else {
                        $data = Http::withHeaders($headers)->get($url, $arguments)->json();
                    }
                    break;
                case "post":
                    if ($contentType === "text") {
                        $data = Http::withHeaders($headers)->post($url, $arguments)->body();
                    } else {
                        $data = Http::withHeaders($headers)->post($url, $arguments)->json();
                    }

                    break;
            }

        } catch (\Exception $exception) {
            info("Error: getServiceModule ".$exception->getMessage());
            $this->setResult([]);
        }

        $this->setResult($data);
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

}
