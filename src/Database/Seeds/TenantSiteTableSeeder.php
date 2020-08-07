<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'site_tenant';

        $site_tenant = array(
            array('tenant_id' => '1','site_id' => '1')
        );

        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($site_tenant);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
