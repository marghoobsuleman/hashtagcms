<?php
namespace MarghoobSuleman\HashtagCms\Core\Utils;

use \Illuminate\Http\Client\PendingRequest;

class Result extends PendingRequest
{


    /**
     * To make it more universal.
     * @todo: Add some more method to it
     * @param $data
     * @return array
     */
    public static function forView($data) {

        return collect($data)->all();
    }

}
