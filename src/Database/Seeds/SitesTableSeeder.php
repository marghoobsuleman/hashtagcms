<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date('Y-m-d H:i:s');
        $sites = array(
            array('id' => '1','name' => 'Hashtag CMS','category_id' => '1','theme_id' => '1','tenant_id' => '1','lang_id' => '1','country_id' => '110','under_maintenance' => '0','domain' => 'www.hashtagcms.com','context' => 'rexhashtagcms','favicon' => '','lang_count' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table('sites')->get()->count() == 0) {
            DB::table('sites')->insert($sites);
        } else {
            echo "SeedingError: `sites` table is not empty\n";
        }
    }
}
