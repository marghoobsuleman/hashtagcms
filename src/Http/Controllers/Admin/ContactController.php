<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\Contact;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;


class ContactController extends BaseAdminController
{
    protected $dataFields = ['id','name','email','phone','comment','created_at'];

    protected $dataSource = Contact::class;


    protected $actionFields = array("delete"); //This is last column of the row

    /*protected $bindDataWithAddEdit = array("zones"=>array("dataSource"=>Zone::class, "method"=>"all"),
                                        "currencies"=>array("dataSource"=>Currency::class, "method"=>"all"));*/


}
