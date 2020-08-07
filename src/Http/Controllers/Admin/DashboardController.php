<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

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
                "link"=>"contact"
            ),
            array(
                "label"=>"Comments ".$commentsToday,
                "total"=>Comment::count(),
                "icon"=>"fa fa-comments-o",
                "link"=>"comment"
            ),

        );
        $data['data'] = $dashboardData;
        return htcms_admin_view("dashboard.index", $data);
    }

}
