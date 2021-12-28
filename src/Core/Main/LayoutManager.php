<?php
namespace MarghoobSuleman\HashtagCms\Core\Main;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;


class LayoutManager extends Results
{

    private InfoLoader $infoLoader;
    private array $htmlData;
    private string $baseIndex = '_layout_/index';
    private string $baseServiceIndex = '_services_/index';
    private array $viewAlias = array();
    private array $viewData = array();
    private string $themeFolder;

    private string $cssPath;
    private string $jsPath;
    private string $imgPath;
    private string $resourcePath;
    private string $resourceDir;
    private string $resourceUrl;

    private string $jsFolder;
    private string $cssFolder;
    private string $imageFolder;


    function __construct()
    {
        parent::__construct();

        $this->infoLoader = app()->HashtagCmsInfoLoader;
        $this->themeFolder = config("hashtagcms.info.theme_folder");
        $this->resourceUrl = config("hashtagcms.info.assets_path.base_url");
        $this->resourceDir = $this->resourceUrl.config("hashtagcms.info.assets_path.base_path");
        $this->jsFolder = config("hashtagcms.info.assets_path.js");
        $this->cssFolder = config("hashtagcms.info.assets_path.css");
        $this->imageFolder = config("hashtagcms.info.assets_path.image");
    }

    /**
     * Set meta and html object
     * @param array $data
     * @return void
     */
    public function setFinalObject(array $data):void
    {
        $html = $data['html'];
        $meta = $data['meta'];
        $this->setHtmlObject($html);;
        $this->setMetaObject($meta);

        $this->setBaseIndex($meta['theme']);

    }

    /**
     * Set base index
     * @param array $theme
     * @return void
     */
    public function setBaseIndex(array $theme):void
    {
        $directory = $theme["directory"];
        $baseFolder = config("hashtagcms.info.theme_folder");
        $viewName = $baseFolder.".".$directory."/".$this->baseIndex;
        $viewName = str_replace("/", ".", $viewName);
        $this->setData("baseIndex", $viewName);

        //for service
        $viewName = $baseFolder.".".$directory."/".$this->baseServiceIndex;
        $viewName = str_replace("/", ".", $viewName);
        $this->setData("baseServiceIndex", $viewName);
    }


    /**
     * Get base index file name
     * @return string
     */
    public function getBaseIndex():string
    {
        return $this->getData('baseIndex');
    }

    /**
     * Get base service index file name
     * @return string
     */
    public function getBaseServiceIndex():string
    {
        return $this->getData('baseServiceIndex');
    }

    /**
     * Set meta object
     * @param array $obj
     * @return void
     */
    public function setMetaObject(array $obj):void {
        $this->setData('meta', $obj);
    }

    /**
     * get meta object
     * @param string|null $key
     * @return mixed
     */
    public function getMetaObject(string $key=null):mixed
    {
        return !empty($key) ? $this->getData('meta')[$key]: $this->getData('meta');
    }

    /**
     * Set html object that being set after loadData
     * @param array $obj
     * @return void
     */
    public function setHtmlObject(array $obj) {
        $this->setData('html', $obj);
    }

    /**
     * This is html object
     * @return mixed
     */
    public function getHtmlObject(): mixed
    {
        return $this->getData('html');
    }

    /**
     * Set html data
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setData(string $key, mixed $value):void {
        $this->htmlData[$key] = $value;
    }

    /**
     * Get html data
     * @param string $key
     * @return mixed
     */
    public function getData(string $key):mixed {
       return $this->htmlData[$key] ?? null;
    }

    /**
     * get body content
     * @return string
     */
    public function getBodyContent():string
    {
        return $this->getData("bodyContent");
    }

    /**
     * get header content
     * @return string
     */
    public function getHeaderContent():string
    {
        $html = $this->getHtmlObject();
        return $html['parsed']['header'];
    }

    /**
     * get meta content
     * @return string
     */
    public function getMetaContent():string
    {
        $html = $this->getHtmlObject();
        return $html['head']['metaContent'];
    }

    /**
     * Get page title
     * @return string
     */
    public function getTitle(): string
    {
        $html = $this->getHtmlObject();
        return $html['head']['title'];
    }

    /**
     * Get footer content
     * @return string
     */
    public function getFooterContent(): string
    {
        $html = $this->getHtmlObject();
        return $html['parsed']['footer'];
    }


    /**
     * Get theme object
     * @return array
     */
    public function getThemeObj():array
    {
        return $this->getMetaObject('theme');
    }


    /**
     * Get view name | theme object must be defined
     * @param string|null $name
     * @return string
     */
    private function getViewName(string $name=null):string {
        $name = ($name === null) ? "" : $name;
        $theme = $this->getThemeObj();
        $directory = $theme["directory"];
        //check if has an alias - means it will load module instead of specified in backend
        $alisView = $this->hasInAlias($name);
        $viewName = ($alisView != null) ? $alisView : $name;
        $themeFolder = $this->themeFolder;
        $viewName = $themeFolder.".".$directory.".".$viewName;
        //info("viewName: ".$viewName);
        return str_replace("/", ".", $viewName);
    }


    /**
     * Bind Data for a view
     * @param string $viewName
     * @param array $data
     */
    public function bindDataForView(string $viewName, mixed $data=array()):void
    {
        $viewName = str_replace("/", ".", $viewName);
        $this->viewData[$viewName] = $data;
    }

    /**
     * Get if module has an alternate module - This is mainly assign from Controller
     * @param string|null $viewName
     * @return mixed
     */
    public function hasInAlias(string $viewName=null):mixed {
        $viewName = ($viewName === null) ? "" : $viewName;
        $viewName = str_replace("/", ".", $viewName);
        return isset($this->viewAlias[$viewName]) ? $this->viewAlias[$viewName]["name"] : null;
    }

    /**
     * Get Data for a view
     * @param string $viewName
     * @return mixed
     */
    public function getDataForView(string $viewName): mixed
    {
        $viewName = str_replace("/", ".", $viewName);
        //info("getDataForView ". $viewName." ". json_encode(isset($this->viewData[$viewName]) ? $this->viewData[$viewName] : array()));
        return isset($this->viewData[$viewName]) ? $this->viewData[$viewName] : array();
    }

    /**
     * Bind Data for a view
     * @param string|null $sourceViewName
     * @param string|null $targetViewName
     * @param array|null $data
     */
    public function replaceViewWith(string $sourceViewName=null, string $targetViewName=null, array $data=null):void
    {

        $sourceViewName = ($sourceViewName === null) ? "" : $sourceViewName;
        $targetViewName = ($targetViewName === null) ? "" : $targetViewName;
        $sourceViewName = str_replace("/", ".", $sourceViewName);
        if($data != null) {
            $this->bindDataForView($sourceViewName, $data);
        }

        if(!empty($sourceViewName)) {
            $this->viewAlias[$sourceViewName] = array("name"=>$targetViewName, "data"=>$data);
        }

    }


    /**
     * Parse theme skeleton
     * @param string $skeleton
     * @param int $site_id
     * @return array
     */
    #[ArrayShape(["hooks" => "array", "modules" => "array"])] public function parseSkeleton(string $skeleton, int $site_id): array
    {
        $subject = $skeleton;
        $pattern = "/\%.*?\%/";
        preg_match_all($pattern, $subject, $matches); //PREG_OFFSET_CAPTURE
        $hooksData = array();
        $moduleData = array();
        if(count($matches)>0) {
            $matches = $matches[0];
            foreach($matches as $key=>$val) {
                $current = $val;
                $isHook = str_contains($current, "%{cms.hook.");
                $isModule = str_contains($current, "%{cms.module.");

                if($isHook) {
                    $patterns = array();
                    $patterns[0] = '/%/';
                    $patterns[1] = '/{cms.hook./';
                    $patterns[2] = '/}/';
                    $replacements = array();
                    $replacements[2] = '';
                    $replacements[1] = '';
                    $replacements[0] = '';
                    $name = preg_replace($patterns, $replacements, $current);
                    $info = $this->infoLoader->getHookInfo(trim($name), $site_id);
                    if($info) {
                        $hooksData[] = $info;
                    }
                }
                if($isModule) {
                    $patterns = array();
                    $patterns[0] = '/%/';
                    $patterns[1] = '/{cms.module./';
                    $patterns[2] = '/}/';
                    $replacements = array();
                    $replacements[2] = '';
                    $replacements[1] = '';
                    $replacements[0] = '';
                    $name = preg_replace($patterns, $replacements, $current);
                    $moduleInfo = $this->infoLoader->getModuleInfo($name, $site_id);
                    if($moduleInfo) {
                        $moduleData[] = $moduleInfo;
                    }
                }
            }
        }

        return array("hooks"=>$hooksData, "modules"=>$moduleData);
    }

    /**
     * Get html content by html object (Fetch by all mdules/theme/hook)
     * @param array $theme
     * @param array $mergeData
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function parseBodyContent(array $theme, array $mergeData=[]):string
    {
        $bodyContent = $theme['skeleton'];
        $hooks = $theme['hooks'];
        $modulesInTheme = $theme['modules'];

        $allData = array();
        $infoKeeper = app()->HashtagCmsInfoLoader->getInfoKeeper();
        //info("parseBodyContent: 1");
        //info(json_encode($infoKeeper));
        foreach ($hooks as $key=>$hook) {
            $placeholder = $hook["placeholder"];
            $modules = $hook["modules"];
            //making string
            if(!isset($allData[$placeholder])) {
                $allData[$placeholder] = array();
            }
            //dd("modules", $modules);
            foreach ($modules as $index=>$module) {
                $viewData = $this->getParsedViewData((array)$module, $infoKeeper);
                $allData[$placeholder][] = $viewData;
            }
            //info("placeholder: ".$placeholder);
        }

       // info("parseBodyContent: 2");

        foreach ($modulesInTheme as $index=>$moduleT) {
            $placeholder = $moduleT->placeholder;
            $viewData = $this->getParsedViewData((array)$moduleT, $infoKeeper);
            $allData[$placeholder][] = $viewData;
        }
       // info("parseBodyContent: 3");
        //make string
        foreach ($allData as $placeholder=>$data) {
            $bodyContent = str_replace($placeholder, join("", $data), $bodyContent);
        }
        //info("parseBodyContent: 4");
        //set in layout
        $bodyContent = $this->getEssentialsElements().$bodyContent;
        $this->setData("bodyContent", $bodyContent);
        //info("Setting body content done...");
        return $bodyContent;
    }

    /**
     * Get essentials elements
     * @return string
     */
    private function getEssentialsElements(): string
    {

        $message = session('__hashtagcms_message__');
        $messageError = session('__hashtagcms_message_error__', false);

        $message = ($messageError == false) ? $message : $messageError;

        $css = ($messageError == false) ? config("hashtagcms.redirect_with_message_design.css_success") : config("hashtagcms.redirect_with_message_design.css_error");

        if(is_array($message)) {
            $css = $message['type'];
            $message = $message['message'];
        }
        $css_close = config("hashtagcms.redirect_with_message_design.css_error_close_button");
        $error_close_text = config("hashtagcms.redirect_with_message_design.error_close_text");

        $finalMsg = "";
        if(!empty($message)) {
            $finalMsg = "<div class='$css'>$message <span style='float:right; cursor: pointer' onClick=\"document.getElementById('__hashtagcms__').style.display='none'\" ><i class='$css_close'>$error_close_text</i></span></div>";
        }

        return "<div id='__hashtagcms__'>$finalMsg</div>";
    }

    /**
     * @param array $modules
     * @param array $infoKeeper
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getParsedViewDataFromMultipleModules(array $modules, array $infoKeeper):string
    {
        //dd("modules", $modules);
        $parseData = [];
        foreach ($modules as $index=>$module) {
            $parseData[] = $this->getParsedViewData((array)$module, $infoKeeper);
        }
        return join("", $parseData);
    }

    /**
     * Get View Data
     * @param array $module
     * @param array $infoKeeper
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getParsedViewData(array $module, array $infoKeeper=array()): mixed
    {
        $viewData = "";
        if($module["data_type"] == "Static") {
            //View name is not needed for "Static" module
            $viewData = (isset($module["data"]) && isset($module["data"]["content"])) ?  $module["data"]["content"] : "";

        } else {

            $viewName = $this->getViewName($module["view_name"]);
            $mergeData = $this->getDataForView($module["view_name"]);

            if (View::exists($viewName)) {
                try {
                    $moduleInfo = $module;
                    unset($moduleInfo['data']);
                    unset($moduleInfo['placeholder']);
                    $viewData = $this->view_make($viewName, array("data"=>$module["data"], "infoKeeper"=>$infoKeeper, "moduleInfo"=>$moduleInfo), $mergeData);

                } catch (Exception $error) {
                    info("View Loading error: ".$error->getMessage());
                }
            } else {
                info("Unable to find view: $viewName");
            }
        }
        //info("Data for view: $viewName");
        return $viewData;
    }

    /**
     * Make view
     * @param string $name
     * @param array $data
     * @param array $mergeData
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function view_make(string $name, array $data=array(), array $mergeData=array()):mixed {

        $newData = array_merge($data['data'], $mergeData);
        $data['data'] = $newData;

        $viewData = view()->make($name, $data, $mergeData)->render();

        //check if it has another module
        $pattern = "/\%{cms.module.+}\%/";
        preg_match_all($pattern, $viewData, $matches); //PREG_OFFSET_CAPTURE

        if(sizeof($matches[0]) > 0) {
            $ml = app()->HashtagCms->moduleLoader();
            foreach ($matches[0] as $key=>$val) {
                $val = preg_replace("/\%{cms.module.|}\%/", "", $val);
                $moduleInfo =  $ml->getModuleInfo($val, false);
                $mData = $ml->getModuleData($moduleInfo);
                //dd($moduleInfo, $mData);
                $vData = view()->make($this->getViewName($moduleInfo->view_name), array("data"=>$mData))->render();

                $viewData = str_replace("%{cms.module.$val}%", $vData, $viewData);

            }
        }


        return $viewData;
    }

    /**
     * Get html data object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getHTMLData(array $mergeData=null):array
    {
        info("============== layoutManager: Start. ==============");
        $infoKeeper = $this->infoLoader->getInfoKeeper();

        $mergeData = ($mergeData === null) ? array() : $mergeData;

        $categoryLinkrewrite = $infoKeeper["categoryName"];
        $siteContext = $infoKeeper["siteInfo"]["context"];
        $tenantLinkrewrite = $infoKeeper["tenantInfo"]["link_rewrite"];
        $langCode = $infoKeeper["langInfo"]["iso_code"];
        $micrositeId = 0;
        $clearCache = request()->get("clearCache")==="true";

        $startTime = microtime(true);
        $dataLoader = app()->HashtagCmsDataLoader;
        $requestParams = array("category"=>$categoryLinkrewrite, "site"=>$siteContext,
            "lang"=>$langCode, "tenant"=>$tenantLinkrewrite,
            "microsite"=>$micrositeId, "clearCache"=>$clearCache);

        $data = $dataLoader->loadData($requestParams);

        info("layoutManager: loading data completed, status: ".$data["status"]);

        if($data["status"] == 200) {

            //Set data in layout manager to access later
            info("layoutManager: setFinalObject");
            $this->setFinalObject($data);

            info("layoutManager: setThemePath");

            $this->setThemePath($data['meta']['theme']["directory"]);

            $bodyContent = $this->parseBodyContent($data['meta']['theme']);
            info("layoutManager: after setThemePath");
            $data['html']['parsed'] = array("body"=>$bodyContent,
                "header"=>$data['html']['head']['headerContent'],
                "footer"=>$data['html']['body']['footer']['footerContent']);

            //update again
            $this->setFinalObject($data);
            info("layoutManager: setFinalObject again");

        } else {
            info("Error loading in page: status: $data[status] message: $data[message]");
            //abort($data["status"], $data["message"]);
            return  $data;
        }
        info("============ layoutManager: End. ===========");
        return $data;
    }

    /**
     * @param string $view_name
     * @param mixed $data
     * @param mixed $merge_data
     * @param int|null $status
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view_master(string $view_name, mixed $data, mixed $merge_data, int $status=null)
    {
        $data = array("data"=>$data);
        return response()->view($view_name, $data, 200);
            //->header('Content-Type', 'text/html; charset=UTF-8');
        //return view($view_name, $data, $merge_data);
    }


    /**
     * Set Theme Path
     * @param string $directory
     */
    public function setThemePath(string $directory=''):void
    {
        //set theme path
        $this->resourcePath = $this->resourceDir."/".$directory;

        //css/js media path
        $this->cssPath = $this->resourcePath."/".$this->cssFolder;
        $this->jsPath = $this->resourcePath."/".$this->jsFolder;
        $this->imgPath = $this->resourcePath."/".$this->imageFolder;
    }

    /**
     * Parse String for path
     * @param string|null $str
     * @return string
     */
    public function parseStringForPath(?string $str="", bool $withDomain=false):string {

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
     * Parse string and load some view if needed
     * @param string $string
     * @return string|string[]|null
     * @todo: path etc handled: need to handle dynamic view and template and some php tags
     */
    public function parseStringForView(string $string=''): array|string|null
    {
        return $this->parseStringForPath($string);
    }

    /**
     * get resource path
     * @return string
     */
    public function getResourcePath(): string
    {
        return $this->resourcePath;
    }

    /**
     * Get CSS Path
     * @return string
     */
    public function getCssPath():string
    {
        return $this->cssPath;
    }

    /**
     * Get JS Path
     * @return string
     */
    public function getJsPath():string
    {
        return $this->jsPath;
    }

    /**
     * Get Image path
     * @return string
     */
    public function getImagePath(): string
    {
        return $this->imgPath;
    }

    /**
     * Should use full path or simple url
     * 1. en/web/home - if true
     * 2. /home - if false
     * @return bool
     */
    public function fullPathStyle(): bool
    {
        return $this->infoLoader->getInfoKeeper("foundLang") || $this->infoLoader->getInfoKeeper("foundTenant");
    }



    /**
     * Get Header menu
     * @param string $active
     * @return array
     */
    public function getHeaderMenu(string $active=null) {

        $active = (!empty($active)) ? '' : $active;
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
     * @param array $data
     * @param bool $withLi
     * @param array|null $css
     * @param bool $isChild
     * @return string
     */
    private function makeMenu(array $data=array(), bool $withLi=true, array $css=null, bool $isChild=false): string
    {

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
     * @param array $css
     * @return string
     */
    public function getHeaderMenuHtml(int $maxLimit=-1, array $css=null):string {
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



}
