<?php

namespace MarghoobSuleman\HashtagCms\Core\ViewComposers\Admin;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;


class ProfileComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $users;


    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $isLoggedIn = (Auth::guest()) ? FALSE : TRUE;
        $userInfo = ($isLoggedIn) ? Auth::user() : NULL;
        $userName = ($isLoggedIn) ? $userInfo->name : "";
        $this->users = array("isLoggedIn"=>$isLoggedIn, "user"=>$userInfo, "userName"=>$userName);

        $view->with('userInfo', $this->users);
    }
}
