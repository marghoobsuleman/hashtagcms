<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers;

use MarghoobSuleman\HashtagCms\Core\Traits\FeEssential;
use Illuminate\Http\Request;
use MarghoobSuleman\HashtagCms\Core\Main\ModuleLoader;

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

    /**
     * Set module mandatory check
     * @param bool $value
     * @return void
     */
    public function setModuleMandatoryCheck(bool $value):void
    {
        ModuleLoader::setMandatoryCheck(false); // this will check module mandatory false
    }
}
