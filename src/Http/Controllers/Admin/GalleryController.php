<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\Gallery;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;

class GalleryController extends BaseAdminController
{

    protected $dataFields = array(
        "id",
        array("label" => "image", "key" => "path", "isImage"=>true),
        "type",
        "group",
        array("label" => "Tags", "key" => "tag.name", "showAllScopes"=>true)
        );

    protected $dataSource = Gallery::class;

    protected $dataWith = ['tag'];

    protected $actionFields = array("edit", "delete");

    protected $bindDataWithAddEdit = array("typeGroups"=>array("dataSource"=>Gallery::class, "method"=>"getTypeGroup"),
        "imageGroups"=>array("dataSource"=>Gallery::class, "method"=>"getImageGroup"));

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request) {

        if(!$this->checkPolicy('edit')) {
            return htcms_admin_view("common.error", Message::getWriteError());
        }
        $data = $request->all();

        $rules = [
            "site_id" => "required",
            "type" => "required|max:50|string",
            "group" => "nullable|max:50|string",
            "key" => "nullable|max:50|string"
        ];
        $module_name = request()->module_info->controller_name;

        //edit
        if(isset($data["id"]) && $data["id"] > 0) {
            //$rules["image"] = "max:255|string";
            $filesRequired = false;
        } else {
            $rules["image"] = "required";
            $filesRequired = sizeof(request()->allFiles()) === 0;
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails() || $filesRequired) {
            if ($filesRequired === true) {
                $validator->errors()->add('image[]', 'Please choose at least one file.');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $saveData["group"] = $data["group"] ?? NULL;
        $saveData["site_id"] = $data["site_id"];
        $saveData["type"] = $data["type"];
        $saveData["updated_at"] = htcms_get_current_date();

        $tags_array = explode(",", $data['tags']);

        //Edit
        if ($data["id"] > 0) {
            if (request()->file("image") != null) {
                $saveData["path"] = $this->upload($module_name, request()->file("image"));
            }
            $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);

            $where = $data["id"];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);

            //Tags
            (new $this->dataSource)->saveTags($data["id"], $tags_array);

        } else {
            //Add
            $allFiles = request()->allFiles()['image'];

            for ($count=0; $count<sizeof($allFiles); $count++) {
                $saveData["path"] = $this->upload($module_name, $allFiles[$count]);
                $saveData["created_at"] = htcms_get_current_date();
                $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);
                //This is in base controller
                $savedData = $this->saveData($arrSaveData);
                if (sizeof($tags_array)) {
                    (new $this->dataSource)->saveTags($savedData["id"], $tags_array);
                }
            }
        }

        $viewData["id"] = $savedData["id"];
        $viewData["saveData"] = $data;
        $viewData["backURL"] = $data["backURL"];
        $viewData["isSaved"] = $savedData["isSaved"];

        return htcms_admin_view("common.saveinfo", $viewData);
    }

    /**
     * Get all images
     * @return mixed
     */
    public function getAllImages() {
        return $this->dataSource::orderBy("id", "desc")->with($this->dataWith)->where("type", "image")->get();
    }

    /**
     * Search images
     * @return mixed
     */
    public function searchImages($tag) {
        return $this->dataSource::with($this->dataWith)->whereHas('tag', function($q) use ($tag) {
            $q->where('name', 'like', '%'.$tag.'%');
        })->get();
    }

    /**
     * Upload images
     * @param Request $request
     * @return array
     */
    public function uploadImages(Request $request)
    {
        $data = $request->all();
        $module_name = request()->module_info->controller_name;

        $allFiles = request()->allFiles();//["images"];
        $allFiles = $allFiles["images"];
        $saveData["group"] = $data["group"] ?? NULL;
        $saveData["site_id"] = htcms_get_site_id();
        $saveData["type"] = "image";
        $saveData["updated_at"] = htcms_get_current_date();
        $saveData["created_at"] = htcms_get_current_date();
        $ids = [];

        for ($count=0; $count<sizeof($allFiles); $count++) {
            $saveData["path"] = $this->upload($module_name, $allFiles[$count]);
            $arrSaveData = array("model"=>$this->dataSource,  "data"=>$saveData);
            $tags_array = explode(",", $data['tags']);
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);
            if (sizeof($tags_array)) {
                (new $this->dataSource)->saveTags($savedData["id"], $tags_array);
            }
            $ids[] = $savedData["id"];
        }
        return ["status"=>true, "message"=>"Images uploaded successfully", "data"=>$this->dataSource::find($ids)];
    }


}

