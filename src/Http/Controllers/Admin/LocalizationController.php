<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use MarghoobSuleman\HashtagCms\Http\Controllers\Controller;


use MarghoobSuleman\HashtagCms\Models\Localization;

class LocalizationController extends BaseAdminController
{

    public function index($more = null)
    {
        return htcms_admin_view("common.index");
    }

}
