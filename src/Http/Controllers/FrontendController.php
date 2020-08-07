<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends FrontendBaseController
{

    public function __construct(Request $request)
    {

        parent::__construct($request);

    }

    /**
     *
     * Render page (@override)
     * @param Request $request
     * @param array $infoKeeper
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        //return $request->infoKeeper;
        return parent::index($request);

    }
}
