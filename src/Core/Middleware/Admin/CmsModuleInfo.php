<?php

namespace MarghoobSuleman\HashtagCms\Core\Middleware\Admin;

use Closure;

use MarghoobSuleman\HashtagCms\Models\CmsModule;

class CmsModuleInfo
{

  protected $adminModule;


  function __construct(CmsModule $adminModuleInfo) {

      $this->adminModule = $adminModuleInfo;

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

        //check if /admin/ has no controller next to it.
        $name = (sizeof($segments)>1) ? $segments[1] : config("hashtagcmsadmin.cmsInfo.defaultPage");

        //Set in request to fetch it later
        $request->module_info = $this->adminModule::getInfoByName($name);
        
        $result =  $next($request);
        //info("cmsModuleInfo: Moving to Next middleware");
        return  $result;
    }
}
