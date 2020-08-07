<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteLangsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Y-m-d H:i:s');
        $site_langs = array(
            array('site_id' => '1','lang_id' => '1','title' => 'Welcome to #CMS','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table('site_langs')->get()->count() == 0) {

            DB::table('site_langs')->insert($site_langs);
        } else {
            echo "SeedingError: `site_langs` table is not empty\n";
        }
    }
}
