<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Page;

//Keeping out of hahatgcms controller scope;
class AnalyticsController extends Controller
{

    /**
     * Publish data
     * @return false|string
     */
    public function publish() {
        $data = \request()->post();
        if(isset($data['categoryId']) && $data['categoryId'] > 0) {
            Category::withoutGlobalScopes()->find((int)$data['categoryId'])->increment('read_count');
        }
        if(isset($data['pageId']) && $data['pageId'] > 0) {
            Page::withoutGlobalScopes()->find($data['pageId'])->increment('read_count');
        }
        return json_encode(array("success"=>true));
    }

}
