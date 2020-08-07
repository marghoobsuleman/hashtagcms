<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;
use Illuminate\Http\Request;


/****
 * Class FrontendBaseController
 * Renderer
 * @package MarghoobSuleman\HashtagCms\Http\Controllers
 */

class FrontendBaseController extends Controller
{

    use FeEssential;

    public function __construct(Request $request)
    {
        //Init theme folder
        //$this->initThemeFolder();
    }

}
