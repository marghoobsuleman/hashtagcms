<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;
use MarghoobSuleman\HashtagCms\Core\Main\LayoutManager;

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

        $isContentRequired = LayoutManager::getMandatoryCheck();

        try {
            info("============ Start loading data from request ============= ");
            if(isset($data['status']) && $data['status']!=200) {
                abort($data['status'], $data['message']);
            }

            //check mandatory module
            if ($data['isContentFound'] == false  && $isContentRequired == true) {
                logger()->error("Content not found!");
                abort(Response::HTTP_NOT_FOUND, "Content not found!");
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
        LayoutManager::setMandatoryCheck($value); // this will check module mandatory false
    }
}
