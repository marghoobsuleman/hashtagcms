<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IssueTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'issue_types';
        $table_name_langs = 'issue_type_langs';

        $issue_types = array(
            array('id' => '1','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => '2020-04-24 18:16:54','deleted_at' => NULL),
            array('id' => '2','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => '2020-04-24 18:16:53','deleted_at' => NULL),
            array('id' => '3','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => '2020-04-24 18:16:51','deleted_at' => NULL),
            array('id' => '4','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => '2020-04-24 18:16:49','deleted_at' => NULL),
            array('id' => '5','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => '2020-04-24 18:16:46','deleted_at' => NULL),
            array('id' => '6','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => '2020-04-24 18:14:14','deleted_at' => NULL),
            array('id' => '7','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '8','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '9','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '10','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '11','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '12','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '13','site_id' => '1','publish_status' => '1','position' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL)
        );

        $issue_types_langs = array(
            array('issue_type_id' => '1','lang_id' => '1','name' => 'Public Health Department','created_at' => '2020-04-24 15:57:37','updated_at' => '2020-04-24 18:36:04','deleted_at' => NULL),
            array('issue_type_id' => '2','lang_id' => '1','name' => 'Enforcement Department','created_at' => '2020-04-24 16:01:10','updated_at' => '2020-04-24 18:36:19','deleted_at' => NULL),
            array('issue_type_id' => '3','lang_id' => '1','name' => 'Fire Department','created_at' => '2020-04-24 16:02:14','updated_at' => '2020-04-24 18:36:32','deleted_at' => NULL),
            array('issue_type_id' => '4','lang_id' => '1','name' => 'PTU Department','created_at' => '2020-04-24 16:02:35','updated_at' => '2020-04-24 18:36:44','deleted_at' => NULL),
            array('issue_type_id' => '5','lang_id' => '1','name' => 'Horticulture Department','created_at' => '2020-04-24 16:03:02','updated_at' => '2020-04-24 18:36:55','deleted_at' => NULL),
            array('issue_type_id' => '6','lang_id' => '1','name' => 'Welfare Department','created_at' => '2020-04-24 16:03:15','updated_at' => '2020-04-24 18:37:06','deleted_at' => NULL),
            array('issue_type_id' => '7','lang_id' => '1','name' => 'Electricity -I','created_at' => '2020-04-24 18:38:17','updated_at' => '2020-04-24 18:38:17','deleted_at' => NULL),
            array('issue_type_id' => '8','lang_id' => '1','name' => 'Electricity-II','created_at' => '2020-04-24 18:38:32','updated_at' => '2020-04-24 18:38:32','deleted_at' => NULL),
            array('issue_type_id' => '9','lang_id' => '1','name' => 'Commercial Department','created_at' => '2020-04-24 18:39:00','updated_at' => '2020-04-24 18:39:00','deleted_at' => NULL),
            array('issue_type_id' => '10','lang_id' => '1','name' => 'Civil Engineering Department-I','created_at' => '2020-04-24 18:39:12','updated_at' => '2020-04-24 18:40:29','deleted_at' => NULL),
            array('issue_type_id' => '11','lang_id' => '1','name' => 'Civil Engineering Department-II','created_at' => '2020-04-24 18:40:11','updated_at' => '2020-04-24 18:40:19','deleted_at' => NULL),
            array('issue_type_id' => '12','lang_id' => '1','name' => 'Medical Services','created_at' => '2020-04-24 18:41:27','updated_at' => '2020-04-24 18:41:27','deleted_at' => NULL),
            array('issue_type_id' => '13','lang_id' => '1','name' => 'EBR Department','created_at' => '2020-04-24 18:41:58','updated_at' => '2020-04-24 18:41:58','deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($issue_types);
            DB::table($table_name_langs)->insert($issue_types_langs);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
