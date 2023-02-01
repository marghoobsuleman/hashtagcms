<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;


use Illuminate\Support\Facades\Session;

/**
 * Using session for now
 */
class SessionManager extends Session
{

    private string $infoKeeperKey = "infoKeeper";

    function __construct()
    {
        
    }

    /**
     * Is exists in key
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return \session()->exists($key);
    }

    /**
     * Put value in session
     * @param $key
     * @param $value
     * @return void
     */
    public function put(string $key, mixed $value):void {
        \session()->put($key, $value);
    }

    /**
     * Get from session
     * @param $key
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public function get($key): mixed
    {
        return \session($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setInfoKeeper(string $key, mixed $value):void
    {
        if(!$this->exists($this->infoKeeperKey)) {
            $this->put($this->infoKeeperKey, array());
        }
        $infoKeeper = $this->get($this->infoKeeperKey);
        $infoKeeper[$key] = $value;
        $this->put($this->infoKeeperKey, $infoKeeper);
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getInfoKeeper(string $key=null):mixed
    {
        //create a blank namespace if not exists
        if(!$this->exists($this->infoKeeperKey)) {
            $this->put($this->infoKeeperKey, array());
        }

        $infoKeeper = $this->get($this->infoKeeperKey);

        if($key == null) {
            return $infoKeeper;
        }

        return $infoKeeper[$key] ?? null;
    }
}
