<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;
use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Page;


class AnalyticsController extends Controller
{

    /**
     * Publish data
     * @return false|string
     */
    public function publish() {
        $data = \request()->post();
        if(isset($data['categoryId']) && $data['categoryId'] > 0) {
            Category::find((int)$data['categoryId'])->increment('read_count');
        }
        if(isset($data['pageId']) && $data['pageId'] > 0) {
            Page::find($data['pageId'])->increment('read_count');
        }
        return json_encode(array("success"=>true));
    }

}
