<?php

namespace MarghoobSuleman\HashtagCms\Core\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

trait FeEssential {


    /**
     * Load data
     * InfoKeeper already has site, category, platform, and category info (Interceptor Middleware)
     * @param Request $request
     */
    public function index(Request $request) {
        
        $layoutManager =  app()->HashtagCms->layoutManager();
        try {
            info("============ Start loading data from request ============= ");
            $data = $layoutManager->getHTMLData();
            if($data['status'] != 200) {
                abort($data['status'], $data['message']);
            }
            if($data["isLoginRequired"] && Auth::id() == null) {
                $category = $layoutManager->getMetaObject("category");
                $category = $category['link_rewrite'];
                $reqParams = request()->all();
                return Redirect::to("/login?redirect=/$category?".http_build_query($reqParams,'', '&'));
            }

        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            info("Error Loading in page.");
            info($e->getMessage());
            exit($e->getMessage());
        }
        info("============ End loading data from request ============= ");
        return $layoutManager->view_master($layoutManager->getBaseIndex(), $data, array());
    }

    /**
     * Bind Data for a view
     * @param string $viewName
     * @param array $data
     * @return void
     */
    protected function bindDataForView(string $viewName, array $data=array()):void
    {
        app()->HashtagCms->layoutManager()->bindDataForView($viewName, $data);
    }

    /**
     * Bind Data for a view
     * @param string|null $sourceViewName
     * @param string|null $targetViewName
     * @param array|null $data
     */
    protected function replaceViewWith(string $sourceViewName=null, string $targetViewName=null, array $data=null):void
    {
        app()->HashtagCms->layoutManager()->replaceViewWith($sourceViewName, $targetViewName, $data);
    }


    /**
     * @todo: should not work after refoctoring
     * Set everything to render a page
     * @param array $obj
     *              - site
     *              - language
     *              - platform
     *              - theme
     *              - category
     */
    protected function setEverything(array $obj) {

        $info = array('site'=>$obj['site'],
            'language'=>$obj['language'],
            'platform'=>$obj['platform'],
            'theme'=>$obj['theme'],
            'category'=>$obj['category'],
        );

        $infoLoader = app()->HashtagCmsInfoLoader;
        //set info
        $infoLoader->setObjInfo($info);

        $infoLoader->setMultiContextVars($obj['category']->id, $obj['site']->id, $obj['platform']->id, $obj['microsite']);
        $infoLoader->setLanguageId($obj['language']->id, $obj['language']->iso_code);

        app()->HashtagCms->setThemePath($obj['theme']->directory);
        app()->HashtagCms->setTheme($obj['theme']);

    }
}

