<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use MarghoobSuleman\HashtagCms\Core\Helpers\Message;
use MarghoobSuleman\HashtagCms\Models\CmsLog;

class CmslogController extends BaseAdminController
{
    protected $dataFields = array("id", "user.name",'module.name', "record_id", "action_performed", "created_at");

    protected $dataSource = CmsLog::class;

    protected $dataWith = ['user', 'module'];

    protected $actionFields = array(); //This is last column of the row

    protected $moreActionFields = array(
        array("label"=>"Show all info",
            "css"=>"js_ajax",
            "icon_css"=>"fa fa-info-circle",
            "hrefAttributes"=>["data-info"=>"cmslog", "data-editable"=>false, "data-excludefields"=>["user", "module"]],
            "action"=>"showinfo",
            "action_append_field"=>"id"
            )
    );


}
