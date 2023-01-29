<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;
use Illuminate\Http\Request;
use MarghoobSuleman\HashtagCms\Core\Main\ModuleLoader;

/****
 * Class FrontendBaseController
 * Renderer
 * @package MarghoobSuleman\HashtagCms\Http\Controllers
 */

class FrontendBaseController extends Controller
{

    use FeEssential;


    public function __construct(Request $request)
    {
        //Init theme folder
    }

    /**
     * Load data
     * InfoKeeper already has site, category, platform, and category info (Interceptor Middleware)
     * @param Request $request
     */
    public function index(Request $request) {
        $layoutManager =  app()->HashtagCms->layoutManager();
        $infoLoader = app()->HashtagCms->infoLoader();
        $data = $layoutManager->init();
        

        try {
            info("============ Start loading data from request ============= ");
            if(isset($data['status']) && $data['status']!=200) {
                //abort($data['status'], $data['message']);
                return $data;
            }

            if($data["isLoginRequired"] && Auth::id() == null) {
                $category = $infoLoader->getCategoryData();
                $category = $category['linkRewrite'];
                $reqParams = request()->all();
                return Redirect::to("/login?redirect=/$category?".http_build_query($reqParams,'', '&'));
            }

        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            info("Error Loading in page.");
            info($e->getMessage());
            exit($e->getMessage());
        }
        info("============ End loading data from request ============= ");
        return $layoutManager->viewMaster($layoutManager->getBaseIndex(), array(), array());
    }

    /**
     * Set module mandatory check
     * @param bool $value
     * @return void
     */
    public function setModuleMandatoryCheck(bool $value):void
    {
        ModuleLoader::setMandatoryCheck(false); // this will check module mandatory false
    }
}
