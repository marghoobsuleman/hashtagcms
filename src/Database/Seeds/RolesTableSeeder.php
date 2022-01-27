<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $table_name = 'roles';
        $date = date('Y-m-d H:i:s');
        $roles = array(
            array('id' => '1','name' => 'super-duper-admin','label' => 'Super Duper Admin','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'super-admin','label' => 'Super Admin','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','name' => 'admin','label' => 'Admin','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','name' => 'editor','label' => 'Editor','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','name' => 'approver','label' => 'Approver','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '6','name' => 'contributor','label' => 'Contributor','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '7','name' => 'ReadOnly','label' => 'Read Only','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );


        if(DB::table($table_name)->get()->count() == 0) {
          DB::table($table_name)->insert($roles);
      }

        $table_name = 'role_user';

        $role_user = array(
            array('role_id' => '1','user_id' => '1'),
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($role_user);
        }
    }
}
