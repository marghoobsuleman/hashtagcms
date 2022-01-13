<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

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
        $infoKeeper = app()->HashtagCmsInfoLoader->getInfoKeeper();
        //check it's blog home
        if(empty($infoKeeper['callableValue'][0])) {
            $this->setModuleMandatoryCheck(false); //in base controlle

            $perPage = config("hashtagcms.blog_per_page");
            $category_link_rewrite = $infoKeeper['category_link_rewrite'];

            $moreCategories = config("hashtagcms.more_categories_on_blog_listing");
            if(sizeof($moreCategories) > 0) {
                $moreCategories[] = $category_link_rewrite;
                $results = Page::getLatestBlog($infoKeeper['siteId'], $infoKeeper['langId'], $moreCategories, $perPage);
            } else {
                $results = Page::getLatestBlog($infoKeeper['siteId'], $infoKeeper['langId'], $category_link_rewrite, $perPage);
            }

            $data['results'] = $results;

            //replace mandatory module with new module.
            $this->replaceViewWith("story", "stories", $data);

            $forComments = array("isBlogHome"=>true);
            $this->bindDataForView("story-comments", $forComments);

            return parent::index($request);
        }

        return parent::index($request);

    }

    public function story($path, $params)
    {
        return $path;
    }

}
