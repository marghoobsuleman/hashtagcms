<?php

namespace MarghoobSuleman\HashtagCms\Core\ViewComposers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use MarghoobSuleman\HashtagCms\Models\CmsModule;
use MarghoobSuleman\HashtagCms\Models\User;

class CmsModuleComposer
{
    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        //info("CmsModuleComposer");
        $allModules = CmsModule::getAdminModules();

        $user = User::find(Auth::user()->id);
        $modulesAllowed = $user->cmsmodules()->get();
        $isAdmin = $user->isSuperAdmin();
        $view->with('allModules', $allModules);
        $view->with('moduleAllowed', $modulesAllowed);
        $view->with('isAdmin', $isAdmin);
    }
}
