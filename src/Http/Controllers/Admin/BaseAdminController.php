<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use MarghoobSuleman\HashtagCms\Core\Traits\Admin\AdminCrud;
use MarghoobSuleman\HashtagCms\Core\Traits\Admin\BaseAdmin;
use MarghoobSuleman\HashtagCms\Core\Traits\Admin\LogManager;
use MarghoobSuleman\HashtagCms\Core\Traits\Admin\UploadManager;

class BaseAdminController extends BaseController
{
    use AdminCrud, AuthorizesRequests, BaseAdmin,
        DispatchesJobs, LogManager, UploadManager, ValidatesRequests;

    public function __construct(Request $request)
    {

        //Some session for layout
        if (! Session::has('layout')) {
            $request->session()->put('layout', 'table');
        }
        //if there is param in url
        if ($request->get('layout')) {
            $layoutType = ($request->get('layout') == 'grid') ? 'grid' : 'table';
            $request->session()->put('layout', $layoutType);
        }

    }

    /**
     * Get By Id
     *
     * @param  int  $id
     * @return array
     */
    protected function getById($id = 0)
    {

        $dataSource = $this->getDataSource();
        $dataWith = $this->getDataWith();

        $data['results'] = [];

        if ($id > 0) {
            $data['results'] = $dataSource::getById($id, $dataWith);
        }

        return $data;

    }
}
