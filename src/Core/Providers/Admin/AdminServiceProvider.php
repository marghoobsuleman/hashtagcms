<?php

namespace MarghoobSuleman\HashtagCms\Core\Providers\Admin;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use MarghoobSuleman\HashtagCms\Core\Policies\CmsPolicy;
use MarghoobSuleman\HashtagCms\Core\ViewComposers\Admin\CmsModuleComposer;
use MarghoobSuleman\HashtagCms\Models\CmsPermission;
use MarghoobSuleman\HashtagCms\Models\Permission;

class AdminServiceProvider extends ServiceProvider
{
    protected $policies = [
        CmsPermission::class => CmsPolicy::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPolicies();

        $allPermission = $this->getPermission();

        if ($allPermission != null) {

            foreach ($allPermission as $permission) {

                Gate::define($permission->name, function ($user) use ($permission) {
                    return (($user->hasRole($permission->roles)) || $user->isSuperAdmin()) && $user->user_type == 'Staff';
                });

            }
        }

        //Only For Admin
        $theme = config('hashtagcmsadmin.cmsInfo.theme');

        View::composer($theme.'.common.*', CmsModuleComposer::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get permission
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     */
    protected function getPermission()
    {
        try {
            return Permission::with('roles')->get();
        } catch (\Exception $e) {
            logger($e->getMessage());

            return null;
        }

    }
}
