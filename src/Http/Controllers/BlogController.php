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
        $infoLoader = app()->HashtagCms->infoLoader();
        $siteData = $infoLoader->getSiteData();
        $langData = $infoLoader->getLangData();
        $categoryData = $infoLoader->getCategoryData();
        dd($infoLoader);
        //check it's blog home
        if(empty($infoKeeper['callableValue'][0])) {
            $this->setModuleMandatoryCheck(false); //in base controlle

            $perPage = config("hashtagcms.blog_per_page");
            $categoryLinkRewrite = $categoryData['linkRewrite'];

            $moreCategories = config("hashtagcms.more_categories_on_blog_listing");
            if(sizeof($moreCategories) > 0) {
                $moreCategories[] = $categoryLinkRewrite; //add one more
                $results = Page::getLatestBlog($siteData['id'], $langData['id'], $moreCategories, $perPage);
            } else {
                $results = Page::getLatestBlog($siteData['id'], $langData['id'], $categoryLinkRewrite, $perPage);
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

    public function story($arg1, $arg2)
    {
        return ["From blog contoller", $arg1, $arg2];
    }

}
