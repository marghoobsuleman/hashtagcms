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

            $perPage = config("hashtagcms.blog_per_page");

            $blog_categories = config("hashtagcms.blog_categories");
            if(sizeof($blog_categories) == 0) {
                $pathInfo = parse_url(url()->current());
                $url = ltrim($pathInfo['path'], "/");
            } else {
                $url = $blog_categories;
            }

            ModuleLoader::setMandatoryCheck(false);
            $results = Page::getLatestBlog(htcms_get_site_id(), htcms_get_language_id(), $url, $perPage);
            $data['results'] = $results;
            $this->replaceViewWith("story", "stories", $data);

            $forComments = array("isBlogHome"=>true);
            $this->bindDataForView("story-comments", $forComments);
            return parent::index($request);
        }

        return parent::index($request);

    }


}
