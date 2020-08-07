<?php

return [
    'namespace'=>'MarghoobSuleman\\HashtagCms\\',
    'context'=>env("CONTEXT", "hashtagcms"),
    'info'=> [
        'theme_folder' => 'hashtagcms::fe',
        'site_name'=>'Hashtag CMS',
        'base_path' => '',
        'theme' => 'fe.default',
        'resource_dir' => 'assets/hashtagcms/fe',
        'js' => 'js',
        'css' => 'css',
        'image' => 'img',
        'media_path' => '/storage/media',
        'records_per_page' => 20
    ],
    "media" => [
        "upload_path"=>"public/media",
        "http_path"=>"/storage/media"
    ],
    "message"=> [
        "themeNotDefined"=>"<div style='margin: 100px; text-align: center; font-size: 70px; color:#ffffff'>Theme is no theme defined for this category</div>",
        "zeroModuleSelected"=>"<div style='margin: 100px; text-align: center; font-size: 70px; color:mediumvioletred'>There is no module assigned for this category</div>"
    ],
    "default"=>array("lang"=>"en", "tenant"=>"web", "when_default_category_not_found"=>"home"),
    "domains" => array(
        "htcms.monsterindia.com"=>env('CONTEXT', 'hashtagcms')
    )
];
