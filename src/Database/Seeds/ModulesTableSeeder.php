<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'modules';

        $date = date('Y-m-d H:i:s');

        $modules = array(
            array('id' => '1','site_id' => '1','name' => 'Header','alias' => 'MODULE_HEADER','linked_module' => '0','view_name' => 'header','data_type' => 'Query','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'select c.id, c.parent_id, c.is_new, c.has_wap, c.wap_url, c.link_rewrite, c.link_navigation,
cl.name, cl.title, cl.is_external, cs.position, cl.link_relation, cl.target, cl.active_key
from categories c
left join category_langs cl on (cl.category_id = c.id)
left join category_site cs on (cs.category_id = c.id)
where c.deleted_at is null and c.publish_status=1 and c.site_id=:site_id and cl.lang_id=:lang_id and cs.platform_id=:platform_id
and cs.exclude_in_listing = 0
order by cs.position asc','data_key_map' => 'site_id,lang_id,platform_id','description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','site_id' => '1','name' => 'Hero','alias' => 'MODULE_HERO','linked_module' => '0','view_name' => 'hero','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','site_id' => '1','name' => 'Info','alias' => 'MODULE_INFO','linked_module' => '0','view_name' => 'info','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','site_id' => '1','name' => 'Products','alias' => 'MODULE_PRODUCTS','linked_module' => '0','view_name' => 'products','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','site_id' => '1','name' => 'Seo Content','alias' => 'MODULE_SEO_CONTENT','linked_module' => '0','view_name' => 'seo','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '6','site_id' => '1','name' => 'Our Speciality','alias' => 'MODULE_OUR_SPECIALITY','linked_module' => '0','view_name' => 'our-speciality','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '7','site_id' => '1','name' => 'Subscribe','alias' => 'MODULE_SUBSCRIBE','linked_module' => '0','view_name' => 'subscribe','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '8','site_id' => '1','name' => 'Blog','alias' => 'MODULE_BLOG','linked_module' => '0','view_name' => 'blog','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '9','site_id' => '1','name' => 'Footer','alias' => 'MODULE_FOOTER','linked_module' => '0','view_name' => 'footer','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '10','site_id' => '1','name' => 'Category Text','alias' => 'MODULE_CATEGORY_TEXT','linked_module' => '0','view_name' => 'category-text','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '11','site_id' => '1','name' => 'Login','alias' => 'MODULE_LOGIN','linked_module' => '0','view_name' => 'auth/login','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '12','site_id' => '1','name' => 'Register','alias' => 'MODULE_REGISTER','linked_module' => '0','view_name' => 'auth/register','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '13','site_id' => '1','name' => 'Reset Password Email','alias' => 'MODULE_RESET_PASSWORD_EMAIL','linked_module' => '0','view_name' => 'auth/passwords/email','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '14','site_id' => '1','name' => 'Reset Password Reset','alias' => 'MODULE_RESET_PASSWORD_RESET','linked_module' => '0','view_name' => 'auth/passwords/reset','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '15','site_id' => '1','name' => 'Story','alias' => 'MODULE_STORY','linked_module' => '0','view_name' => 'story','data_type' => 'Query','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'select c.id as cat_id, p.id, p.parent_id, p.site_id, p.microsite_id, p.platform_id, p.category_id, 
       p.alias, p.exclude_in_listing, p.content_type, p.position, p.link_rewrite, p.link_navigation, p.menu_placement, 
       p.header_content, p.footer_content, p.insert_by, p.update_by, p.enable_comments, p.required_login, 
       p.publish_status, p.read_count, p.attachment, p.img, p.author, p.content_source, p.created_at, 
       p.updated_at, p.read_count, pl.name, pl.title, pl.description, pl.page_content, pl.link_relation, 
       pl.target, pl.active_key, pl.meta_title, pl.meta_keywords, pl.meta_description, pl.meta_robots, 
       pl.meta_canonical from pages p 
           left join page_langs pl on (p.id = pl.page_id) 
           left join categories c on(p.category_id = c.id) 
           left join (SELECT page_id as comment_page_id, COUNT(*) as comments_count FROM comments where deleted_at is null GROUP BY page_id) cmn ON (cmn.comment_page_id = p.id) where p.link_rewrite=:link_rewrite and p.site_id=:site_id and pl.lang_id=:lang_id and p.publish_status=1 and c.id=:category_id and p.deleted_at is null;','data_key_map' => 'link_rewrite,site_id,lang_id,category_id','description' => NULL,'is_mandatory' => '1','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '1','live_edit' => '0', 'shared' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '16','site_id' => '1','name' => 'Test','alias' => 'MODULE_TEST','linked_module' => '0','view_name' => 'test','data_type' => 'Static','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'CONTENT_TEST','data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '17','site_id' => '1','name' => 'Profile','alias' => 'MODULE_PROFILE','linked_module' => '0','view_name' => 'profile','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '18','site_id' => '1','name' => 'Contact','alias' => 'MODULE_CONTACT','linked_module' => '0','view_name' => 'contact','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '19','site_id' => '1','name' => 'Stories','alias' => 'MODULE_STORIES','linked_module' => '0','view_name' => 'stories','data_type' => 'Query','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'select c.link_rewrite as category_link_rewrite, u.name as user_name, p.read_count,
p.id, p.site_id, p.microsite_id, p.platform_id, p.category_id, p.alias, p.exclude_in_listing, p.content_type, p.position, p.link_rewrite, p.menu_placement,
p.enable_comments, p.created_at, p.updated_at, pl.name, pl.title, pl.description, pl.page_content, pl.link_relation, pl.target, pl.active_key, cmn.*
from pages p 
left join page_langs pl on (p.id = pl.page_id) 
left join categories c on(c.id=p.category_id)
left join users u on(u.id=p.insert_by)
left join (SELECT page_id as comment_page_id, COUNT(*) as comments_count FROM comments where deleted_at is null GROUP BY page_id) cmn ON cmn.comment_page_id = p.id
where p.site_id=:site_id and pl.lang_id=:lang_id and p.publish_status=1 and p.content_type=\'blog\' and p.deleted_at is null 
order by p.created_at DESC limit 10;','data_key_map' => 'site_id,lang_id','description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '20','site_id' => '1','name' => 'Social','alias' => 'MODULE_SOCIAL','linked_module' => '0','view_name' => 'social','data_type' => 'Query','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'select * from site_props where site_id=:site_id and group_name="SocialLinks" and is_public=1 and deleted_at is null;','data_key_map' => 'site_id','description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '21','site_id' => '1','name' => 'Example Custom','alias' => 'MODULE_EXAMPLE_CUSTOM','linked_module' => '0','view_name' => 'example/custom','data_type' => 'Custom','query_statement' => NULL,'query_as' => NULL,'data_handler' => NULL,'data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '22','site_id' => '1','name' => 'Example Query','alias' => 'MODULE_EXAMPLE_QUERY','linked_module' => '0','view_name' => 'example/query','data_type' => 'Query','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'select * from comments where deleted_at is null limit 5','data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '23','site_id' => '1','name' => 'Example Service','alias' => 'MODULE_EXAMPLE_SERVICE','linked_module' => '0','view_name' => 'example/service','data_type' => 'Service','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'https://picsum.photos/v2/list?limit=4','data_key_map' => NULL,'description' => NULL,'is_mandatory' => '0','method_type' => 'GET','service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '24','site_id' => '1','name' => 'Example QueryService','alias' => 'MODULE_EXAMPLE_QUERYSERVICE','linked_module' => '0','view_name' => 'example/queryservice','data_type' => 'QueryService','query_statement' => 'select * from site_props where site_id=:site_id and group_name="SocialLinks" and is_public=1 and deleted_at is null;','query_as' => 'data','data_handler' => 'https://picsum.photos/v2/list?page=2&limit=4','data_key_map' => 'site_id','description' => NULL,'is_mandatory' => '0','method_type' => 'GET','service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '25','site_id' => '1','name' => 'Example UrlService','alias' => 'MODULE_EXAMPLE_URLSERVICE','linked_module' => '0','view_name' => 'example/urlservice','data_type' => 'UrlService','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'https://picsum.photos/v2/list?page=2&limit=:limit','data_key_map' => 'limit','description' => NULL,'is_mandatory' => '1','method_type' => 'GET','service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '26','site_id' => '1','name' => 'Story Comments','alias' => 'MODULE_STORY_COMMENTS','linked_module' => '0','view_name' => 'story-comments','data_type' => 'Query','query_statement' => NULL,'query_as' => NULL,'data_handler' => 'select name, email, comment, category_id, page_id, created_at from comments
where
page_id=(select id from pages where link_rewrite=:link_rewrite) and category_id=:category_id
and site_id=:site_id and deleted_at is null order by id desc;','data_key_map' => 'link_rewrite,site_id,category_id','description' => NULL,'is_mandatory' => '0','method_type' => NULL,'service_params' => NULL,'individual_cache' => '0','cache_group' => NULL,'is_seo_module' => '0','live_edit' => '0', 'shared' => '0','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($modules);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }

        $this->moduleSiteSeed();
    }

    private function moduleSiteSeed() {
        $table_name = 'module_site';
        $date = date('Y-m-d H:i:s');
        $module_site = array(
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '1','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '1','hook_id' => '9','module_id' => '9','position' => '2','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '1','hook_id' => '10','module_id' => '2','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '1','hook_id' => '10','module_id' => '4','position' => '2','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '1','hook_id' => '10','module_id' => '5','position' => '4','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '1','hook_id' => '10','module_id' => '6','position' => '3','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '1','hook_id' => '10','module_id' => '7','position' => '5','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '2','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '2','hook_id' => '9','module_id' => '9','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '2','hook_id' => '10','module_id' => '15','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '2','hook_id' => '10','module_id' => '26','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '3','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '3','hook_id' => '9','module_id' => '9','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '3','hook_id' => '10','module_id' => '11','position' => '2','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '4','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '4','hook_id' => '9','module_id' => '9','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '4','hook_id' => '10','module_id' => '12','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '5','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '5','hook_id' => '9','module_id' => '9','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '5','hook_id' => '10','module_id' => '13','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '6','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '6','hook_id' => '9','module_id' => '9','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '6','hook_id' => '10','module_id' => '16','position' => '2','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '6','hook_id' => '10','module_id' => '21','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '6','hook_id' => '10','module_id' => '22','position' => '3','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '6','hook_id' => '10','module_id' => '23','position' => '4','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '6','hook_id' => '10','module_id' => '24','position' => '5','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '6','hook_id' => '10','module_id' => '25','position' => '6','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '7','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '7','hook_id' => '9','module_id' => '9','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '7','hook_id' => '10','module_id' => '17','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '8','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '8','hook_id' => '9','module_id' => '9','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '8','hook_id' => '10','module_id' => '18','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '9','hook_id' => '1','module_id' => '1','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '9','hook_id' => '9','module_id' => '9','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '9','hook_id' => '10','module_id' => '15','position' => '1','publish_status' => '1','insert_by' => '1','update_by' => '1','approved_by' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($module_site);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
