<?php

namespace MarghoobSuleman\HashtagCms\Core\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

use MarghoobSuleman\HashtagCms\Core\DataLoader;
use MarghoobSuleman\HashtagCms\Core\ModuleLoader;
use MarghoobSuleman\HashtagCms\HashtagCms;

trait FeEssential {

    private $infoObj = array();
    private $themeFolder;
    private $viewData = array();
    private $viewAlias = array();
    protected $common;
    protected $baseIndex = "_layout_/index";

    public function __construct(Request $request)
    {

        $this->initThemeFolder();
        $this->common = app()->Common;
    }

    /**
     * Set theme folder
     */
    private function initThemeFolder() {
        $this->themeFolder = config("hashtagcms.info.theme_folder");
    }

    /**
     * Bind Data for a view
     * @param $viewName
     * @param array $data
     */
    protected function bindDataForView(string $viewName, $data=array()) {
        $viewName = str_replace("/", ".", $viewName);
        $this->viewData[$viewName] = $data;
    }

    /**
     * Bind Data for a view
     * @param string $sourceViewName
     * @param string $targetViewName
     * @param array $data
     */
    protected function replaceViewWith(string $sourceViewName='', string $targetViewName='', $data=null) {
        $sourceViewName = str_replace("/", ".", $sourceViewName);
        if($data != null) {
            $this->bindDataForView($sourceViewName, $data);
        }

        $this->viewAlias[$sourceViewName] = array("name"=>$targetViewName, "data"=>$data);
    }

    /**
     * Get if module has an alternate module - This is mainly assign from Controller
     * @param string $viewName
     * @return bool|null
     */
    private function hasInAlias(string $viewName='') {
        $viewName = str_replace("/", ".", $viewName);
        return isset($this->viewAlias[$viewName]) ? $this->viewAlias[$viewName]["name"] : null;
    }

    /**
     * Get Data for a view
     * @param $viewName
     * @return array|mixed
     */
    protected function getDataForView(string $viewName) {
        $viewName = str_replace("/", ".", $viewName);
        //info("ViewName: ".$viewName);
        return isset($this->viewData[$viewName]) ? $this->viewData[$viewName] : array();
    }


    /**
     * Load data
     * InfoKeeper already has site, category, tenant, and category info (Interceptor Middleware)
     * @param Request $request
     * @param array $infoKeeper
     * @return array
     */
    public function index(Request $request) {

        $infoKeeper = $request->infoKeeper;

        $mergeData = [];

        $category = $infoKeeper["categoryName"];
        $context = $infoKeeper["siteInfo"]["context"];
        $tenant = $infoKeeper["tenantInfo"]["link_rewrite"];
        $lang = $infoKeeper["langInfo"]["iso_code"];
        $microsite = 0;
        $parsedData = $this->getCategoryContent($category, $context, $tenant, $lang, $microsite, $mergeData);

        if($parsedData['status'] >= 400) {
            $category = $parsedData["data"]["category"]["link_rewrite"];
            return Redirect::to("/login?redirect=".$category);
        }

        $data = $parsedData["data"];

        $theme = $parsedData["theme"];

        return $this->viewMaster($theme, $this->baseIndex, $data);
    }


    /**
     * @param $category
     * @param $lang
     * @param $tenant
     * @param $context
     * @param $microsite
     * @param $mergeData
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function getCategoryContent($category, $context, $tenant, $lang, $microsite, $mergeData) {

        $startTime = microtime(true);

        $this->initThemeFolder();
        $loader = new DataLoader();

        $data = $loader->loadData($category, $lang, $tenant, $context, $microsite);

        if($data == null || $data["status"] != 200) {
            info("category not found. ".$category);
            abort($data["status"], $data["message"]);

        } else {

            if ($data["isLoginRequired"] && Auth::id() == null) {
                $data["status"] = 403;
            }

            //Check if category has a theme
            info("category data is loaded...");

            $htmlData = $data["html"];
            $theme = $htmlData["theme"];

            //remove theme node
            unset($data["html"]["theme"]["hooks"]); //we don't need hooks info


            //Add theme
            $this->setTheme($theme);

            //To pass some info to each views
            $infoKeeperForView = array("langs"=>$data["langs"],
                "tenant"=>$data["tenant"],
                "site"=>$data["site"],
                "category"=>$data["category"]
            );
            info("Fetching module content");

            //Handle if there is no module assign for this category
            if($htmlData["totalModules"] > 0) {
                try {
                    $bodyContent = $this->getBodyContent($htmlData, $infoKeeperForView, $mergeData);
                } catch (\Exception $exception) {
                    info("There is some error in loading module ".$exception->getMessage());
                }

            } else {
                $bodyContent = config("hashtagcms.message.zeroModuleSelected");
            }

            info("Module content fetch is completed");
            $esel = $this->getEssentialsElements();
            $data["bodyContent"] = $esel.$bodyContent;

            $profiling = array("processedTime"=>(microtime(true) - $startTime));
            $data["profiling"] = $profiling;

            $data["HashtagCms"] = \MarghoobSuleman\HashtagCms\HashtagCms::class;
            $rData["data"] = $data;
            $rData["isLoginRequired"] = $data['isLoginRequired'];
            $rData["status"] = $data["status"];
            $rData["theme"] = $theme;

            $this->common = app()->Common;

            $this->common->setFinalData("bodyContent", $data["bodyContent"]);
            $this->common->setFinalData("profiling", $data['profiling']);
            $this->common->setFinalData("tenant", $data['tenant']);
            $this->common->setFinalData("langs", $data['langs']);
            $this->common->setFinalData("site", $data['site']);
            $this->common->setFinalData("category", $data['category']);
            $this->common->setFinalData("html", $data["html"]);
            $this->common->setFinalData("data", $data);
            $this->common->setFinalData("isLoginRequired", $data['isLoginRequired']);
            $this->common->setFinalData("status", $data["status"]);
            $this->common->setFinalData("theme", $theme);

            return $rData;
        }

        abort(404, "I am sorry, Don't know what to do!");

    }

    /**
     * Get essentials elements
     * @return string
     */
    private function getEssentialsElements() {

        $message = session('__hashtagcms_message__');
        $messageError = session('__hashtagcms_message_error__', false);

        $message = ($messageError == false) ? $message : $messageError;

        $css = ($messageError == false) ? 'success' : 'error';

        if(is_array($message)) {
            $css = $message['type'];
            $message = $message['message'];
        }

        $ele = "<div id='__hashtagcms__'><alert-message data-message='$message' data-type='$css'></alert-message></div>";
        return $ele;
    }


    /**
     * Get html content by html object (Fetch by all mdules/theme/hook)
     * @param array $htmlObject
     * @param array $infoKeeper
     * @param array $mergeData
     * @return mixed
     */
    private function getBodyContent(array $htmlObject=array(), array $infoKeeper=array(), array $mergeData=[]) {

        $this->initThemeFolder();

        $html = $htmlObject;
        $theme = $html["theme"];
        //info($theme);
        $hooks = $theme["hooks"];

        $skeleton = $theme["skeleton"];
        $hookData = array();
        $moduleData = array();
        //dd($hooks);
        foreach ($hooks as $key=>$hook) {
            info($hook["info"]["alias"]);
            $hookInfo = $hook["info"];
            //dd($hook);
            $type = $hook["type"];
            $alias = "%{cms.".$type.".".$hookInfo["alias"]."}%";
            info("alias : $alias, hookType: $type");
            if($type == "hook") {

                $modules = $hook["modules"];

                if(!isset($hookData[$alias])) {
                    $hookData[$alias] = array();
                }

                foreach ($modules as $index=>$module) {

                    $viewData = $this->getParsedViewData($module, $infoKeeper);

                    $hookData[$alias][] = $viewData;

                }
            } else {

                $hookData[$alias][] = $this->getParsedViewData($hook, $infoKeeper);
            }
        }
        //dd("hookData", $hookData);
        //replace hook with module data
        foreach ($hookData as $hook=>$module) {
            $skeleton = str_replace($hook, join("", $module), $skeleton);
        }

        //replace images etc
        $common = app()->Common;

        $skeleton = $common->parseStringForPath($skeleton);

        return $skeleton;
    }

    /**
     * Get View Data
     * @param $obj
     * @param $infoKeeper
     * @return string
     */
    public function getParsedViewData($obj, $infoKeeper=array()) {
        $viewData = "";

        if($obj["data_type"] == "Static") {

            $viewData = (isset($obj["data"]) && isset($obj["data"]["content"])) ?  $obj["data"]["content"] : "";

        } else {

            $viewName = $this->getViewName($obj["view_name"]);
            $mergeData = $this->getDataForView($obj["view_name"]);

            if (View::exists($viewName)) {
                try {
                    //$viewData = $this->view_make($viewName, array("data"=>$module["data"]));
                    $viewData = $this->view_make($viewName, array("data"=>$obj["data"], "infoKeeper"=>$infoKeeper), $mergeData);

                } catch (Exception $error) {
                    info("View Loading error: ".$error->getMessage());
                }
            } else {
                info("Unable to find view: $viewName");
            }
        }
        return $viewData;
    }


    /**
     * Get view name | theme object must be define
     * @param string $name
     * @return mixed
     */
    private function getViewName(string $name='') {

        $theme = $this->getTheme();
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
     * @param array $themeObj
     * @param string $viewName
     * @param array $data
     * @param array $mergeData
     * @return mixed
     */
    private function viewMaster(array $themeObj, string $viewName='', array $data=array(), array $mergeData=[]) {
        $directory = $themeObj["directory"];
        $baseFolder = $this->themeFolder;
        $viewName = $baseFolder.".".$directory.".".$viewName;
        $viewName = str_replace("/", ".", $viewName);

        return $this->view($viewName, $data, $mergeData);
    }

    /**
     * Load View.
     *
     * @param $name
     * @param array $data
     * @return mixed
     */

    public function view($name, $data=array(), array $mergeData=[]) {

        return view($name, $data, $mergeData);
    }

    /**
     * Make view
     * @param string $name
     * @param array $data
     * @param array $mergeData
     * @param string $viewData
     * @return string
     */
    public function view_make(string $name, $data=array(), array $mergeData=array()) {

        $newData = array_merge($data['data'], $mergeData);
        $data['data'] = $newData;

        //getModuleByAlias
        $viewData = view()->make($name, $data, $mergeData)->render();

        //check if it has another module
        $pattern = "/\%{cms.module.+}\%/";
        preg_match_all($pattern, $viewData, $matches); //PREG_OFFSET_CAPTURE

        if(sizeof($matches[0]) > 0) {

            $ml = new ModuleLoader();
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
     * Set theme object
     * @param $themObj
     */
    public function setTheme($themObj) {
        $this->initThemeFolder();
        $this->infoObj["theme"] = $themObj;
    }

    /**
     * Get theme object
     * @return mixed
     */
    public function getTheme() {
        return $this->infoObj["theme"];
    }

    /**
     * Set everything to render a page
     * @param array $obj
     *              - site
     *              - language
     *              - tenant
     *              - theme
     *              - category
     */
    public function setEverything(array $obj) {

        $info = array('site'=>$obj['site'],
            'language'=>$obj['language'],
            'tenant'=>$obj['tenant'],
            'theme'=>$obj['theme'],
            'category'=>$obj['category'],
        );

        //set info
        $this->common->setInfo($info);

        $this->common->setContextVars($obj['category']->id, $obj['site']->id, $obj['tenant']->id, $obj['microsite']);
        $this->common->setLanguageId($obj['language']->id, $obj['language']->iso_code);
        $this->common->setThemePath($obj['theme']->directory);
        $this->setTheme($obj['theme']);

    }
}

