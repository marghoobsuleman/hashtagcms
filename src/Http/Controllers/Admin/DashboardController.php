<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use MarghoobSuleman\HashtagCms\Models\Category;
use MarghoobSuleman\HashtagCms\Models\Comment;
use MarghoobSuleman\HashtagCms\Models\Contact;
use MarghoobSuleman\HashtagCms\Models\Subscriber;

class DashboardController extends BaseAdminController
{


    public function index($more = null)
    {
        $cCount = Contact::today()->count();
        $contactToday = ($cCount > 0 ) ? " ( $cCount <span class='new'>new</span> )" : "";

        $sCount = Subscriber::today()->count();
        $subscriberToday = ($sCount > 0 ) ? " ( $sCount <span class='new'>new</span> )" : "";

        $commentsCount = Comment::today()->count();
        $commentsToday = ($commentsCount > 0 ) ? " ( $commentsCount <span class='new'>new</span> )" : "";

        $topPageLimit = htcms_admin_config("chartPages", true);

        $graphData['categories'] = Category::getReadCounts($topPageLimit);
        $graphData['pages'] = Category::getContentReadCounts($topPageLimit);

        $dashboardData = array(
            array(
                "label"=>"Contacts ".$contactToday,
                "total"=>Contact::count(),
                "icon"=>"fa fa-telegram",
                "link"=>"contact"
            ),
            array(
                "label"=>"Subscribers ".$subscriberToday,
                "total"=>Subscriber::count(),
                "icon"=>"fa fa-handshake-o",
                "link"=>"subscriber"
            ),
            array(
                "label"=>"Comments ".$commentsToday,
                "total"=>Comment::count(),
                "icon"=>"fa fa-comments-o",
                "link"=>"comment"
            ),

        );
        $data['data'] = $dashboardData;
        $data['graphData'] = json_encode($graphData);
        return htcms_admin_view("dashboard.index", $data);
    }

}
