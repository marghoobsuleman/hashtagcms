<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'cms_modules';
        $date = date('Y-m-d H:i:s');
        $cms_modules = array(
            array('id' => '1','name' => 'Sites','controller_name' => 'site','display_name' => NULL,'parent_id' => '0','sub_title' => 'Site','icon_css' => 'fa fa-globe','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '9','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'Localization','controller_name' => 'localization','display_name' => NULL,'parent_id' => '0','sub_title' => 'Localization','icon_css' => 'fa fa-star-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '2','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','name' => 'Countries','controller_name' => 'country','display_name' => NULL,'parent_id' => '2','sub_title' => 'Country','icon_css' => 'fa fa-map-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '4','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','name' => 'Cities','controller_name' => 'city','display_name' => NULL,'parent_id' => '2','sub_title' => 'Manage Cities','icon_css' => 'fa fa-building-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '5','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','name' => 'Settings','controller_name' => 'setting','display_name' => NULL,'parent_id' => '0','sub_title' => 'Settings','icon_css' => 'fa fa-gear','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '21','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '6','name' => 'CMS Modules','controller_name' => 'cmsmodule','display_name' => NULL,'parent_id' => '5','sub_title' => 'CMS Modules Controller','icon_css' => 'fa fa-cogs','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '22','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '7','name' => 'Dashboard','controller_name' => 'dashboard','display_name' => NULL,'parent_id' => '0','sub_title' => 'Dashboard','icon_css' => 'fa fa-dashboard','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '8','name' => 'Zones','controller_name' => 'zone','display_name' => NULL,'parent_id' => '2','sub_title' => 'Zones','icon_css' => 'fa fa-map-marker','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '3','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '9','name' => 'Currencies','controller_name' => 'currency','display_name' => NULL,'parent_id' => '2','sub_title' => 'Manage Currency','icon_css' => 'fa fa-dollar','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '7','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '10','name' => 'Authors','controller_name' => 'author','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage Authors','icon_css' => 'fa fa-user','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '8','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '11','name' => 'Permission','controller_name' => 'permission','display_name' => NULL,'parent_id' => '6','sub_title' => 'Manage users permission','icon_css' => 'fa fa-lock','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '11','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '12','name' => 'Roles','controller_name' => 'role','display_name' => NULL,'parent_id' => '5','sub_title' => 'Manage Roles','icon_css' => 'fa fa-user-secret','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '23','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '13','name' => 'Layout','controller_name' => 'layout','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage layout','icon_css' => 'fa fa-paperclip','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '12','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '14','name' => 'Content','controller_name' => 'page','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manager Pages','icon_css' => 'fa fa-newspaper-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '18','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '16','name' => 'Theme','controller_name' => 'theme','display_name' => NULL,'parent_id' => '13','sub_title' => 'Mange theme','icon_css' => 'fa fa-object-group','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '13','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '17','name' => 'Frontend Modules','controller_name' => 'module','display_name' => NULL,'parent_id' => '13','sub_title' => 'Manage Modules','icon_css' => 'fa fa-cogs','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '14','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '18','name' => 'Categories','controller_name' => 'category','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage Categories','icon_css' => 'fa fa-folder-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '17','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '19','name' => 'Tenant','controller_name' => 'tenant','display_name' => NULL,'parent_id' => '1','sub_title' => 'Manage Tenant','icon_css' => 'fa fa-superpowers','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '10','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '21','name' => 'Hook','controller_name' => 'hook','display_name' => NULL,'parent_id' => '13','sub_title' => 'Manage Hooks','icon_css' => 'fa fa-anchor','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '15','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '22','name' => 'Roles Rights','controller_name' => 'rolesright','display_name' => NULL,'parent_id' => '5','sub_title' => 'Roles rights key','icon_css' => 'fa fa-key','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '24','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '23','name' => 'Static Module','controller_name' => 'staticmodule','display_name' => NULL,'parent_id' => '13','sub_title' => 'Static module','icon_css' => 'fa fa-puzzle-piece','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '16','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '32','name' => 'Homepage','controller_name' => 'homepage','display_name' => 'Homepage Manager','parent_id' => '0','sub_title' => 'Homepage Manager','icon_css' => 'fa fa-file-code-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '20','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '33','name' => 'Language','controller_name' => 'language','display_name' => NULL,'parent_id' => '2','sub_title' => 'Manage Languages','icon_css' => 'fa fa-language','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '6','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '38','name' => 'Site Properties','controller_name' => 'siteprop','display_name' => NULL,'parent_id' => '1','sub_title' => 'Manager Site Properties','icon_css' => 'fa fa-cogs','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '11','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '39','name' => 'oAuth 2.0','controller_name' => 'oauth','display_name' => NULL,'parent_id' => '5','sub_title' => 'Manage oAuth','icon_css' => 'fa fa-lock','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '25','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '40','name' => 'Contacts','controller_name' => 'contact','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage Contacts','icon_css' => 'fa fa-telegram','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '26','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '41','name' => 'Subscribers','controller_name' => 'subscriber','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage Subscribers','icon_css' => 'fa fa-handshake-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '27','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '42','name' => 'Blog','controller_name' => 'blog','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage Blogs','icon_css' => 'fa fa-quote-right','list_view_name' => NULL,'edit_view_name' => 'page/addedit','position' => '19','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '43','name' => 'Comments','controller_name' => 'comment','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage Comments','icon_css' => 'fa fa-comments-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '29','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($cms_modules);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
