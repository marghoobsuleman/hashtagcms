<?php

return [
    'namespace'=>'MarghoobSuleman\\HashtagCms\\',
    'context'=>env("CONTEXT", "hashtagcms"),
    'info'=> [
        'theme_folder' => 'hashtagcms::fe',
        'site_name'=>'Hashtag CMS',
        'base_path' => '',
        'theme' => 'fe.default',
        'assets_path'=>array('base'=>'/assets/hashtagcms/fe',
                            'js'=>'js', 'css'=>'css', 'image'=>'img'),
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
    "redirect_with_message_design"=> [
        'css_success'=>'alert-success alert mb-0 appear',
        'css_error'=>'alert-danger alert mb-0 appear',
        'css_error_close_button'=>'fa fa-times',
        'error_close_text'=>''
    ],
    "default"=>array("lang"=>"en", "tenant"=>"web", "when_default_category_not_found"=>"home"),
    "domains" => array(
        "dev.hashtagcms.com"=>env('CONTEXT', 'hashtagcms')
    ),
    "blog_per_page"=>10,
    "more_categories_on_blog_listing"=>array(), //"support"
    "api_secrets"=>array(
        /**
         * This is a combination of context and random key. will be used in /api/hashtagcms/public/mobiles/configs/v1/site-configs?site=htcmsctx&api_secret=61c58507bbac1
         */
        "htcmsctx"=>"61c58507bbac1"
    )
];
