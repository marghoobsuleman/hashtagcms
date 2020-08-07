<?php

namespace MarghoobSuleman\HashtagCms\Core\Middleware\Admin;

use Closure;

use MarghoobSuleman\HashtagCms\Models\CmsModule;

class CrayonModuleInfo
{

  protected $adminModule;


  function __construct(CmsModule $admin_module_info) {

      $this->adminModule = $admin_module_info;

  }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $segments = request()->segments();

        $name = (sizeof($segments)>1) ? $segments[1] : config("hashtagcmsadmin.cmsInfo.defaultPage");

        $request->module_info = $this->adminModule::getInfoByName($name);

        $result =  $next($request);
        //info("crayonModuleInfo: Moving to Next middleware");
        return  $result;
    }
}
