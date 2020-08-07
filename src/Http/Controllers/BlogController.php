<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use MarghoobSuleman\HashtagCms\Core\ModuleLoader;
use MarghoobSuleman\HashtagCms\Models\Page;
use Illuminate\Http\Request;

class BlogController extends FrontendBaseController
{

    /**
     *
     * Render page (@override)
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {

        //check it's blog home
        if(empty($request->infoKeeper['callableValue'][0])) {
            ModuleLoader::setMandatoryCheck(false);
            $data = Page::getLatestBlog(htcms_get_site_id(), htcms_get_language_id());

            $this->replaceViewWith("story", "stories", $data);
            return parent::index($request);
        }

        return parent::index($request);

    }


}
