<?php

namespace MarghoobSuleman\HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use MarghoobSuleman\HashtagCms\Models\Subscriber;
use MarghoobSuleman\HashtagCms\Core\Helpers\Message;


class SubscriberController extends BaseAdminController
{
    protected $dataFields = ['id','email','name','subscribed_for','created_at'];

    protected $dataSource = Subscriber::class;

}
