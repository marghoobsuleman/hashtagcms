<?php

return [
    'namespace'=>'MarghoobSuleman\\HashtagCms\\',
    'context'=>env("CONTEXT", "hashtagcms"),
    'info'=> [
        'view_folder' => 'hashtagcms::fe',
        'site_name'=>'Hashtag CMS',
        'base_path' => '',
        'theme' => 'fe.default',
        /***
         * In case you want to set up a different CDN
         * for the different site
         */
        /*'assets_path'=>array("dev.hashtagcms.com"=>array('base_url'=>env('ASSET_URL', 'https://images.hashtagcms.org/media'),
                                    'base_path'=>'/assets/hashtagcms/fe',
                                    'js'=>'js', 'css'=>'css', 'image'=>'img')
        ),*/
        'assets_path'=>array('base_url'=>env('ASSET_URL', ''),
            'base_path'=>'/assets/hashtagcms/fe',
            'js'=>'js', 'css'=>'css', 'image'=>'img'),
        'media_path' => '/storage/media',
        'records_per_page' => 20
    ],
    "media" => [
        "upload_path"=>"public/media",
        "http_path"=>"/storage/media"
    ],
    "message"=> [
        "zeroModuleSelected"=>"<div style='margin: 100px; text-align: center; font-size: 70px; color:mediumvioletred'>There is no module assigned for this category</div>"
    ],
    "redirect_with_message_design"=> [
        'css_success'=>'alert alert-primary mb-0 appear',
        'css_error'=>'alert-danger alert mb-0 appear',
        'css_error_close_button'=>'fa fa-times',
        'error_close_text'=>''
    ],
    "blog_per_page"=>10,
    "more_categories_on_blog_listing"=>array("support"), //"support"

    /**
     * This is needed to run same site on multiple domain
     * and when you want to run via external api you need to have
     * domain and context key combination here to run the site.
     */
    "domains" => array(
        "dev.hashtagcms.com"=>env('CONTEXT', 'jflcms'),
        "api.dominos.co.in"=>'jflcms',
        "api.hashtagcms.com"=>'htcms'

    ),
    "api_secrets"=>array(
        /**
         * Do not forget the change this key when you go in production
         * This is a combination of context and random key.
         * will be used in api ie /api/hashtagcms/public/configs/v1/site-configs?site=htcmsctx&api_secret=61d2aeb489bb8df385
         * api_key as header will be set with the same value
         */
        "htcms"=>env('API_SECRET', "61c58507bbac1")
    )
];
