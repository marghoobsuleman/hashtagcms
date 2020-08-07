<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'categories';
        $table_name_langs = 'category_langs';
        $table_name_site = 'category_site';
        $date = date('Y-m-d H:i:s');
        $categories = array(
            array('id' => '1','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '1','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => '/','link_navigation' => NULL,'link_rewrite_pattern' => NULL,'has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '0','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '0','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => 'blog','link_navigation' => NULL,'link_rewrite_pattern' => '{link_rewrite?}','has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '0','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '0','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => 'login','link_navigation' => NULL,'link_rewrite_pattern' => NULL,'has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '0','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '0','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => 'register','link_navigation' => NULL,'link_rewrite_pattern' => NULL,'has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '0','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '0','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => 'password','link_navigation' => NULL,'link_rewrite_pattern' => NULL,'has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '0','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '6','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '0','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => 'example','link_navigation' => NULL,'link_rewrite_pattern' => NULL,'has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '0','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '7','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '0','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => 'profile','link_navigation' => NULL,'link_rewrite_pattern' => NULL,'has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '1','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '8','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '0','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => 'contact','link_navigation' => NULL,'link_rewrite_pattern' => NULL,'has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '0','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '9','parent_id' => NULL,'site_id' => '1','is_site_default' => '0','is_root_category' => '0','is_new' => '0','has_wap' => '0','wap_url' => NULL,'link_rewrite' => 'support','link_navigation' => NULL,'link_rewrite_pattern' => '{link_rewrite}','has_some_special_module' => '0','special_module_alias' => NULL,'required_login' => '0','insert_by' => '1','update_by' => '1','publish_status' => '1','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL)
        );


        $category_langs = array(
            array('category_id' => '1','lang_id' => '1','name' => 'Home','title' => 'Home','excerpt' => 'home excerpt...','content' => 'content home...','active_key' => 'home','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => NULL,'target' => NULL,'meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '2','lang_id' => '1','name' => 'Blog','title' => 'Find our latest blog','excerpt' => NULL,'content' => NULL,'active_key' => 'blog','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => 'alternate','target' => '_blank','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '3','lang_id' => '1','name' => 'Login','title' => 'Login','excerpt' => NULL,'content' => NULL,'active_key' => 'login','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => NULL,'target' => NULL,'meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '4','lang_id' => '1','name' => 'Register','title' => 'Register','excerpt' => NULL,'content' => NULL,'active_key' => 'register','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => NULL,'target' => NULL,'meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '5','lang_id' => '1','name' => 'Reset Password','title' => 'Reset Password','excerpt' => NULL,'content' => NULL,'active_key' => 'password','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => NULL,'target' => NULL,'meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '6','lang_id' => '1','name' => 'Example','title' => 'Example','excerpt' => NULL,'content' => NULL,'active_key' => 'example','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => NULL,'target' => NULL,'meta_title' => 'Example','meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '7','lang_id' => '1','name' => 'Profile','title' => 'Profile','excerpt' => NULL,'content' => NULL,'active_key' => 'Profile','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => 'alternate','target' => '_blank','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '8','lang_id' => '1','name' => 'Contact','title' => 'Contact','excerpt' => NULL,'content' => NULL,'active_key' => 'contact','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => 'alternate','target' => '_blank','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '9','lang_id' => '1','name' => 'Support','title' => 'Support','excerpt' => NULL,'content' => NULL,'active_key' => 'support','third_party_mapping_key' => NULL,'b2b_mapping' => NULL,'is_external' => '0','link_relation' => NULL,'target' => NULL,'meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        $category_site = array(
            array('category_id' => '1','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '0','position' => '1','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '1','site_id' => '1','tenant_id' => '2','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '0','position' => NULL,'cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '2','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '0','position' => '7','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '2','site_id' => '1','tenant_id' => '2','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '0','position' => '2','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '3','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '1','position' => '5','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '4','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '1','position' => '2','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '5','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '1','position' => '3','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '6','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '0','position' => '4','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '7','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '1','position' => '6','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '8','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '0','position' => '8','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('category_id' => '9','site_id' => '1','tenant_id' => '1','theme_id' => '1','icon' => NULL,'icon_css' => NULL,'header_content' => NULL,'footer_content' => NULL,'exclude_in_listing' => '1','position' => '9','cache_category' => NULL,'created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );



        if(DB::table($table_name)->get()->count() == 0) {

            DB::table($table_name)->insert($categories);

            DB::table($table_name_langs)->insert($category_langs);

            DB::table($table_name_site)->insert($category_site);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
