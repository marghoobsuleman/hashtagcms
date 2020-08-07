<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticModuleContentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'static_module_contents';
        $table_name_langs = 'static_module_content_langs';
        $date = date('Y-m-d H:i:s');

        $static_module_contents = array(
            array('id' => '3','site_id' => '1','alias' => 'CONTENT_TEST','update_by' => '1','insert_by' => '1','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL)
        );

        $static_module_content_langs = array(
            array('static_module_content_id' => '3','lang_id' => '1','title' => 'test','content' => ' <section class="sample-module"><div class="container"><div><h2 class="alert-success alert">Custom Module</h2> <p>
This is static module
            </p></div></div>
</section>','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() == 0) {

            DB::table($table_name)->insert($static_module_contents);

            DB::table($table_name_langs)->insert($static_module_content_langs);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
