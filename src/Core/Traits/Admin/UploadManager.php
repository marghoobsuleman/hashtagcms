<?php

namespace MarghoobSuleman\HashtagCms\Core\Traits\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadManager {

    /**
     * @param string $module
     * @param array $files // request()->file("key") or request()->allFiles()
     * @param null $path
     * @return array|mixed|null
     */
    public function upload(string $module='', $files=array(), $path=NULL) {

        $imageSaved = NULL;
        $allFiles = $files;

        $hasFiles = ((isset($allFiles) && is_array($allFiles)  && sizeof($allFiles) > 0) || !empty($allFiles)) ? TRUE : FALSE;

        //Save all - when passed data as request()->allFiles()
        if(is_array($files) && $hasFiles) {

            $imageSaved = array();

            //save multiple
            foreach ($allFiles as $key=>$images) {

                //Save if multiple
                if(is_array($images)) {

                    foreach ($images as $imgKey=>$imgFile) {
                        $imageSaved[$key][] = $this->saveNow($module, $imgFile, $path);
                    }

                } else {
                    //Save single image
                    $imageSaved[$key] = $this->saveNow($module, request()->file($key), $path);
                }
            }

        } else if($hasFiles) {

            //save one - when pass data as file object request()->file('html_input_name')
            $imageSaved = $this->saveNow($module, $files, $path);
        }

        return $imageSaved;

    }



    /**
     * @param string $module
     * @param UploadedFile|NULL $file
     * @param null $path
     * @return mixed
     */
    private function saveNow(string $module='', UploadedFile $file=NULL, $path=NULL) {
        $storedName = "";
        if($module != '' && $file != NULL && $module != NULL) {
            $upload_path = $this->getFolder($module);
            //dd($upload_path);
            $storedName =  Storage::putFile($upload_path, $file);

        } else if($path!=NULL && $file != NULL) {
            $storedName =  Storage::putFile($path, $file);
        }
        //remove basepath and save only folder and filename
        return str_replace(config('hashtagcmsadmin.media.upload_path')."/", "", $storedName);
    }

    /**
     * Get folder for upload
     * @param string $path
     * @param bool $withMonthYear
     * @param bool $makeIfNotExist
     * @return string
     */
    private function getFolder(string $path, bool $withMonthYear=TRUE, bool $makeIfNotExist=FALSE) {
        $month = "";
        $year = "";
        $upload_path = config('hashtagcmsadmin.media.upload_path');
        if($withMonthYear == TRUE) {
            $time = mktime(0, 0, 0);
            $year = "/".date("Y", $time)."/";
            $month = strtolower(date("F", $time));
        }
        $path = strtolower($path);
        $folderPath = $upload_path."/".$path.$year.$month;
        return $folderPath;
    }

}
