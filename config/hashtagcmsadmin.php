<?php

return [
    'cmsInfo'=> [
        'defaultPage'=>'dashboard',
        'site_label' => '#CMS - Admin',
        'base_context' => 'admin',
        'base_path' => '/admin',
        'version' => env('BE_RESOURCE_VERSION', '2020050741'),
        'theme' => 'hashtagcms::be.default',
        'assets_path' => 'assets/hashtagcms/be',
        'app_url' => env("APP_URL"),
        'resource_dir' => 'be/default',
        'media_path' => '/storage/media', //media path
        'show_delete_popup' => false,
        'records_per_page' => 20,
        'action_field_title' => array("label"=>"Action", "key"=>"action"),
        'action_as_ajax' => array('delete', 'approve', 'publish_status'),
        'make_field_as_link'=>array(array("key"=>"id", "action"=>"edit"),
                                    array("key"=>"publish_status", "action"=>"publish",
                                        "css_0"=>"text-warning fa fa-circle-o", "css_1"=>"text-success fa fa-check-square-o"),
                                    ),
        'action_icon_css'=>array(
            'edit'=>'fa fa-edit',
            'delete'=>'fa fa-trash-o',
            'approve'=>'glyphicon glyphicon-ok',
            'loading'=>'fa-spinner fa-pulse fa-fw'
        ),
        'module_types'=>array('Static','Query','Service','Custom','QueryService','UrlService', 'MenuService')
      ],
    "media" => [
        "upload_path"=>"public/media", //-- /storage/app/public/media - run - php artisan storage:link
    ],
    "imageSupportedByBrowsers" => array("apng", "avif", "gif", "jpg","jpeg", "jfif", "pjpeg", "pjp", "png", "svg", "webp", "bmp", "ico", "cur", "tif", "tiff"),
    "chartPages"=>10

];