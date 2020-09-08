<?php
namespace MarghoobSuleman\HashtagCms\Core;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Common
{

    private $globalKeyMap = array();
    private $themeFolder = "";

    private $jsFolder = "js";
    private $cssFolder = "css";
    private $imageFolder = "img";

    private $themPath = "";
    private $cssPath = "";
    private $jsPath = "";
    private $imgPath = "";
    private $resourcePath = "";
    private $resourceDir = "";

    private $infoData = array();
    private $htmlData = array(); //This will be final output


    function __construct()
    {
        $this->themeFolder = config("hashtagcms.info.theme_folder");
        $this->resourceDir = config("hashtagcms.info.resource_dir");
        $this->jsFolder = config("hashtagcms.info.js");
        $this->cssFolder = config("hashtagcms.info.css");
        $this->imageFolder = config("hashtagcms.info.image");

        info("========== init common ===============");
    }


    /**
     * Set context variable to replace in query etc
     * @param int $category_id
     * @param int $site_id
     * @param int $tenant_id
     * @param int $microsite_id
     */
    public function setContextVars(int $category_id, int $site_id, int $tenant_id, int $microsite_id=0):void {

        $this->globalKeyMap[":category_id"]= array("key"=>"category_id", "value"=>$category_id);;
        $this->globalKeyMap[":site_id"] = array("key"=>"site_id", "value"=>$site_id);
        $this->globalKeyMap[":tenant_id"] = array("key"=>"tenant_id", "value"=>$tenant_id);;
        $this->globalKeyMap[":microsite_id"] = array("key"=>"microsite_id", "value"=>$microsite_id);
    }

    /**
     * Get context variable
     * @param string $key
     * @return mixed|null
     */
    public function getContextVar(string $key) {
        return $this->globalKeyMap[$key] ?? null;
    }

    /**
     * Set single variable
     * @param string $key
     * @param $value
     */
    public function setContextVariable(string $key, $value) {
        $this->globalKeyMap[":$key"] = array("key"=>$key, "value"=>$value);
    }

    /**
     * Set Language Id
     * @param int $lang_id
     */
    public function setLanguageId(int $lang_id = 1, $locale=null):void {
        $this->setContextVariable("lang_id", $lang_id);
        if($locale != null) {
            app()->setLocale($locale);
        }
    }

    /**
     * Get Language Id
     * @param int $lang_id
     */
    public function getLanguageId():int {
        return $this->getContextVar("lang_id");
    }

    /**
     * Parse query and get the results
     * @param string $query
     * @return array (optional)
     */
    public function dbSelect(string $query, array $byParams=array()) {
        //$queryParams = $this->findAndReplaceGlobalIds($query);
        $queryParams = (sizeof($byParams)==0) ? $this->findAndReplaceGlobalIds($query) : $byParams;

        /*info("======================== query ============= ");
        info(json_encode($query));
        info(json_encode($queryParams));*/
        try {

            if(sizeof($queryParams) > 0) {
                return DB::select($query, $queryParams);
            }
            return DB::select($query);

        } catch (\Exception $e) {
            info("dbSelect: There is an error: ".$e->getMessage());
        }

    }

    /**
     * Parse query and get the results
     * @param string $query
     * @return array (optional)
     */
    public function dbSelectOne(string $query, array $byParams=array()) {

        $queryParams = (sizeof($byParams)==0) ? $this->findAndReplaceGlobalIds($query) : $byParams;

        info(json_encode($queryParams));;

        /*info("======================== query ============= ");
        info(json_encode($query));
        info(json_encode($queryParams));*/

        try {

            if(sizeof($queryParams) > 0) {
                $data =  DB::selectOne($query, $queryParams);
            } else {
                $data = DB::selectOne($query);
            }

            return  ($data !== null) ? (array)$data : array();

        } catch (Exception $e) {
            info("dbSelectOne: There is an error: ".$e->getMessage());
        }

    }


    /**
     * Parse String for path
     * @param string $str
     * @return string|string[]|null
     */
    public function parseStringForPath(?string $str=""):string {

        if($str != "" && $str!=null) {
            $patterns = array();
            $patterns[0] = '/%{resource_path}%/';
            $patterns[1] = '/%{css_path}%/';
            $patterns[2] = '/%{js_path}%/';
            $patterns[3] = '/%{image_path}%/';

            $replacements = array();
            $replacements[0] = "/".$this->getResourcePath();
            $replacements[1] = $this->getCssPath();
            $replacements[2] = $this->getJsPath();
            $replacements[3] = $this->getImagePath();


            return preg_replace($patterns, $replacements, $str);
        }
        return "";
    }


    /**
     * Get Theme path
     * @return string
     */
    public function getThemePath():string {
        return $this->themPath;
    }

    /**
     * Set Theme Path
     * @param string $directory
     */
    public function setThemePath(string $directory=''):void {
        $this->themPath = $this->themeFolder.".".$directory;
        $this->resourcePath = $this->resourceDir."/".$directory;

        //Css path
        $this->cssPath = htcms_get_domain_path()."/".$this->resourcePath."/".$this->cssFolder;
        $this->jsPath = htcms_get_domain_path()."/".$this->resourcePath."/".$this->jsFolder;
        $this->imgPath = htcms_get_domain_path()."/".$this->resourcePath."/".$this->imageFolder;

    }

    /**
     * Set Image Folder
     * @param string $folder
     */
    public function setImageFolder($folder='') {
        $this->imageFolder = $folder;
    }

    /**
     * Set Css Folder
     * @param string $folder
     */
    public function setCssFolder($folder='') {
        $this->cssFolder = $folder;
    }

    /**
     * Set Js Folder
     * @param string $folder
     */
    public function setJsFolder($folder='') {
        $this->jsFolder = $folder;
    }


    /**
     * get resource path
     * @return string
     */
    public function getResourcePath() {
        return $this->resourcePath;
    }

    /**
     * Get CSS Path
     * @return string
     */
    public function getCssPath():string {
        return $this->cssPath;
    }

    /**
     * Get JS Path
     * @return string
     */
    public function getJsPath():string {
        return $this->jsPath;
    }

    /**
     * Get Image path
     * @return string
     */
    public function getImagePath() {
        return $this->imgPath;
    }

    /**
     * Find and Replace for making query
     * :site_id will be array("site_id"=>1)
     * @param string $str
     * @return array
     */
    private function findAndReplaceGlobalIds(string $str) {

        $subject = $str;
        $pattern = "/:\w+/i";
        preg_match_all($pattern, $subject, $matches); //PREG_OFFSET_CAPTURE
        $arr = array();
        $matches = $matches[0];
        //info("matches: ". json_encode($matches));
        foreach ($matches as $key=>$val) {
            $queryKey = $val;
            $gKey = $this->getContextVar($queryKey);
            if(isset($gKey) && $gKey != null) {
                $arr[$gKey["key"]] = $gKey["value"];
            }
        }
        return $arr;
    }


    /**
     * Set info for current request
     * @param $key
     * @param $value
     */
    public function setInfo($key, $value=null) {
        if(is_array($key)) {
            foreach ($key as $k=>$v) {
                $this->infoData[$k] = $v;
            }
        } else {
            $this->infoData[$key] = $value;
        }

    }

    /**
     * Get info from current request
     * @param string $key
     * @param string|null $subKey
     * @return mixed|null
     */
    public function getInfo(string $key, string $subKey=null) {
        if($subKey == null) {
            return $this->infoData[$key] ?? null;
        }
        return $this->infoData[$key][$subKey] ?? null;
    }

    /**
     * Get info from request object - before loading the category data
     * @param $key
     * @return mixed|null
     */
    public function getFromRequest($key) {
        return $this->infoData[$key] ?? null;
    }


    /**
     * Set data in redis cache
     * @param $key
     * @param $value
     * @param int $expiresAt
     */
    public function put($key, $value, $expiresAt=null) {
        if(env("REDIS_CACHE") == 1) {
            $expiresAt = ($expiresAt != null) ? $expiresAt : env("REDIS_EXPIRES_AT");
            $expiresAt = now()->addMinutes($expiresAt);
            Cache::store('redis')->put($key, $value, $expiresAt);

            Cache::put($key, $value, $expiresAt);
        }
    }

    /**
     * Get data from redis cache
     * @param $key
     * @return mixed|null
     */
    public function get($key) {
        if(env("REDIS_CACHE") == 1) {
            return Cache::get($key);
        }
        return null;
    }

    /**
     * Get Header menu
     * @param string $active
     * @return array
     */
    public function getHeaderMenu($active='-1') {
        $query = "select c.id, c.parent_id, c.is_new, c.has_wap, c.wap_url, c.link_rewrite, 
                    c.link_navigation, cl.name, cl.title, cl.is_external, cs.position, cl.link_relation, 
                    cl.target, cl.active_key from categories c left join category_langs cl on (cl.category_id = c.id) 
                    left join category_site cs on (cs.category_id = c.id) where c.deleted_at is null and c.publish_status=1 
                    and c.site_id=:site_id and cl.lang_id=:lang_id and cs.tenant_id=:tenant_id and 
                    cs.exclude_in_listing = 0 order by cs.position asc";

        $data = $this->dbSelect($query);


        $sortedMenu = array();
        foreach ($data as $index=>$menu) {
            $id = $menu->id;
            $key = "menu_".$id;
            $parent_id = $menu->parent_id;

            if($parent_id == null || $parent_id == 0) {
                $sortedMenu[$key] = (array)$menu;
                if(!isset($sortedMenu[$key]["child"])) {
                    $sortedMenu[$key]["child"] = array();
                }
            }
            if($parent_id>0) {
                $sortedMenu["menu_".$parent_id]["child"][] = (array)$menu;
            }

        }

        //make it sensible
        $allMenu = array();
        foreach ($sortedMenu as $m) {
            if(strtolower($m["active_key"]) == strtolower($active)) {
                $m["is_active"] = true;
            }
            $allMenu[] = $m;
        }

        return $allMenu;
    }

    /**
     * Make menu
     * @param string $data
     * @param bool $withLi
     * @param null $css
     * @param bool $isChild
     * @return string
     */
    private function makeMenu($data='', $withLi=true, $css=null, $isChild=false) {

        $active = htcms_get_category_info('active_key');
        $active_css = ($data["active_key"] == $active) ? ' '.$css['active'] : "";

        $title = $data['title'];

        $link_rewrite = (isset($data["link_navigation"]) && !empty($data["link_navigation"]) && $data["link_navigation"] != null) ? $data["link_navigation"] : $data["link_rewrite"];
        $link_rewrite = htcms_get_path($link_rewrite);

        $text = $data['name'];


        $liEnd = ($withLi == true) ? "</li>" : "";

        $dataCss = ($withLi == true) ? '' : ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ';

        $liCss = ($isChild == true) ? $css['childItem']['li'] : $css['item']['li'];
        $aCss = ($isChild == true) ? $css['childItem']['a'] : $css['item']['a'];

        if($withLi == false) {
            $liCss = $css['itemWithChild']['li'];
            $aCss = $css['itemWithChild']['a'];
        }

        $liCss = $liCss.$active_css;

        //$liStart = ($isChild == true) ? "" : "<li class='$liCss' $dataCss>";
        $liStart = "<li class='$liCss' $dataCss>";

        return "$liStart<a class='$aCss' title='$title' href='$link_rewrite'>$text</a>$liEnd";
    }

    /**
     * Get header Menu HTML
     * @param int $maxLimit
     * @param $css
     * @return string
     */
    public function getHeaderMenuHtml($maxLimit=-1, $css=null):string {
        $data = $this->getHeaderMenu();

        $css = ($css != null) ? $css : array("item"=>array("li"=>"nav-item", "a"=>"nav-link"),
                    "childItem"=>array("li"=>"", "a"=>"dropdown-item"),
                    "itemWithChild"=>array("li"=>"nav-item dropdown", "a"=>"nav-link dropdown-toggle", "group"=>"dropdown-menu"),
                    "active"=>"active"
                    );

        $menuMax = ($maxLimit == -1) ? count($data) :  $maxLimit;
        $shouldAddMore = count($data) > $menuMax;
        $htmlMenu = array();
        foreach ($data as $index=>$menu) {
            if($shouldAddMore && $index == $menuMax-1) {
                $htmlMenu[] = "<li class='".$css['itemWithChild']['li']."'><a data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' class='".$css['itemWithChild']['a']."' title='More' href='#'>More</a>";
                $htmlMenu[] = "<ul class='".$css['itemWithChild']['group']."'>";
            }
            $hasChild = (count($menu["child"])>0) ? true : false;
            $htmlMenu[] = $this->makeMenu($menu, (($hasChild) ? false : true), $css, false);
            if($hasChild) {
                $htmlMenu[] = "<ul class='".$css['itemWithChild']['group']."'>";
                foreach ($menu["child"] as $childMenu) {
                    $htmlMenu[] = $this->makeMenu($childMenu, true, $css, true);
                }
                $htmlMenu[] = "</ul></li>";
            }
            if($shouldAddMore && $index == count($data)-1) {
                $htmlMenu[] = "</ul></li>";
            }

        }

        return join("", $htmlMenu);
    }


    /**
     * Parse string and load some view if needed
     * @todo: path etc handled: need to handle dynamic view and template and some php tags
     * @param string $string
     * @return string|string[]|null
     */
    public function parseStringForView($string='') {
        return $this->parseStringForPath($string);
    }

    /**
     * Should use full path or simple url
     * 1. en/web/home - if true
     * 2. /home - if false
     * @return bool
     */
    public function fullPathStyle() {
        return ($this->getFromRequest("foundLang") || $this->getFromRequest("foundTenant")) ? true : false;
    }

    /**
     * Set this data for output
     * @param string $key
     * @param null $value
     */
    public function setFinalData(string $key, $value=null) {
        $this->htmlData[$key] = $value;
    }

    /**
     * Get html data
     * @param null $key
     * @return mixed|null
     */
    public function getFinalData($key=null) {
        return isset($this->htmlData[$key]) ? $this->htmlData[$key] : null;
    }
}
