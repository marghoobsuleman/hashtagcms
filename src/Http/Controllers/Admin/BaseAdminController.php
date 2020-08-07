<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Session;

use MarghoobSuleman\HashtagCms\Core\Traits\Admin\AdminCrud;
use MarghoobSuleman\HashtagCms\Core\Traits\Admin\UploadManager;
use MarghoobSuleman\HashtagCms\Core\Traits\Admin\BaseAdmin;
use MarghoobSuleman\HashtagCms\Core\Traits\Admin\LogManager;



class BaseAdminController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,
        BaseAdmin, UploadManager, AdminCrud, LogManager;


    function __construct(Request $request)
    {

        //Some session for layout
        if(!Session::has('layout')) {
            $request->session()->put('layout', 'table');
        }
        //if there is param in url
        if($request->get("layout")) {
            $layoutType = ($request->get("layout") == "grid") ? "grid" : "table";
            $request->session()->put('layout', $layoutType);
        }

    }

    /**
     * Get By Id
     * @param int $id
     * @return array
     */

    protected function getById($id=0) {

        $dataSource = $this->getDataSource();
        $dataWith = $this->getDataWith();

        $data['results'] =  array();

        if($id>0) {
            $data['results'] =  $dataSource::getById($id, $dataWith);
        }

        return $data;

    }


}
