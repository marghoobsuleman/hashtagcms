<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

class SettingController extends BaseAdminController
{
    public function index($more = null)
    {
        return htcms_admin_view('common.index');
    }
}
