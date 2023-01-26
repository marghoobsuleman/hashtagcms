<?php
namespace MarghoobSuleman\HashtagCms\Core\Traits;

use MarghoobSuleman\HashtagCms\Models\Theme;
use MarghoobSuleman\HashtagCms\Core\Main\DataLoader;
use MarghoobSuleman\HashtagCms\Core\Enum\LayoutKeys;
use Illuminate\Support\Facades\View;

trait LayoutHandler {
    
    private array $themeCache;
    private DataLoader $dataLoader;

    private string $baseIndex = '_layout_/index';
    private string $baseServiceIndex = '_services_/index';
    private array $layoutData = array();


    /**
     * @param string|null $str
     * @param string|int $theme_dir
     * @return string|null
     */
    public function parseStringForPath(?string $str=null, string|int $theme_dir=null):?string {
        if (empty($str)) {
            return $str;
        }
        //if theme dir is an id; fetch it from Theme
        if (gettype($theme_dir) == "integer") {
            if (isset($this->themeCache[$theme_dir])) {
                $theme = $this->themeCache[$theme_dir];
            } else {
                $this->themeCache[$theme_dir] = $theme = Theme::withoutGlobalScopes()->find($theme_dir); //it's theme id when passed as an integer
            }
            $theme_dir = $theme->directory;
        }
        $host = request()->getHost();

        $assetPath = config("hashtagcms.info.assets_path");

        //Get it by domain or by config
        $assetSource = (isset($assetPath[$host])) ? $assetPath[$host] : $assetPath;


        $resourceUrl = $assetSource['base_url'];
        $resourceDir = $assetSource['base_path'];
        $jsFolder = $assetSource['js'];
        $cssFolder = $assetSource['css'];
        $imageFolder =  $assetSource['image'];


        $resourcePath = $resourceUrl.$resourceDir."/".$theme_dir;

        //css/js media path
        $cssPath = $resourcePath."/".$cssFolder;
        $jsPath = $resourcePath."/".$jsFolder;
        $imgPath = $resourcePath."/".$imageFolder;


        if($str != "" && $str!=null) {
            $patterns = array();
            $patterns[0] = '/%{resource_path}%/';
            $patterns[1] = '/%{css_path}%/';
            $patterns[2] = '/%{js_path}%/';
            $patterns[3] = '/%{image_path}%/';

            $replacements = array();
            $replacements[0] = "/".$resourcePath;
            $replacements[1] = $cssPath;
            $replacements[2] = $jsPath;
            $replacements[3] = $imgPath;
            //info("asset: this->getJsPath() ".asset($this->getJsPath()) ." === ". $str);

            $str = preg_replace($patterns, $replacements, $str);
            return $str;
        }
        return "";
    }



    /**
     * Set base index
     * @param string $directory
     * @return void
     */
    public function setBaseIndex(string $directory):void
    {
        $baseFolder = config("hashtagcms.info.theme_folder");
        $viewName = $baseFolder.".".$directory."/".$this->baseIndex;
        $viewName = str_replace("/", ".", $viewName);
        $this->setData(LayoutKeys::baseIndex, $viewName);

        //for service
        $viewName = $baseFolder.".".$directory."/".$this->baseServiceIndex;
        $viewName = str_replace("/", ".", $viewName);
        $this->setData(LayoutKeys::baseServiceIndex, $viewName);
    }

    /**
     * Get base index file name
     * @return string
     */
    public function getBaseIndex():string
    {
        return $this->getData(LayoutKeys::baseIndex);
    }

    /**
     * Get base service index file name
     * @return string
     */
    public function getBaseServiceIndex():string
    {
        return $this->getData(LayoutKeys::baseServiceIndex);
    }

    

    /**
     * Set layout data
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setData(string $key, mixed $value):void {
        $this->layoutData[$key] = $value;
    }

    /**
     * Get layout data
     * @param string $key
     * @return mixed
     */
    public function getData(string $key):mixed {
        return $this->layoutData[$key] ?? null;
    }







}
