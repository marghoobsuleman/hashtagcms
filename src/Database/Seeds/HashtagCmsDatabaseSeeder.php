<?php
namespace MarghoobSuleman\HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;

class HashtagCmsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Site
        $this->call(SitesTableSeeder::class);
        $this->call(SiteLangsTableSeeder::class);

        //Language
        $this->call(LangsTableSeeder::class);


        //Countries
        $this->call(CountriesTableSeeder::class);
        $this->call(CountryLangsTableSeeder::class);

        //Cities
        $this->call(CitiesTableSeeder::class);

        //Zone
        $this->call(ZonesTableSeeder::class);

        //Currencies
        $this->call(CurrenciesTableSeeder::class);

        //Cms Modules
        $this->call(CmsModulesTableSeeder::class);


        //User and Rights
        $this->call(UserTableSeeder::class);
        //roles
        $this->call(RoleTableSeeder::class);
        //permission
        $this->call(PermissionTableSeeder::class);
        //permission role
        $this->call(PermissionRoleTableSeeder::class);


        //Tenants
        $this->call(TenantsTableSeeder::class);

        //Hooks
        $this->call(HooksTableSeeder::class);

        //Theme
        $this->call(ThemesTableSeeder::class);


        //category
        $this->call(CategoryTableSeeder::class);

        //Modules
        $this->call(ModulesTableSeeder::class);


        //Site wise
        //country
        $this->call(CountrySiteTableSeeder::class); //done

        //currency
        $this->call(CurrencySiteTableSeeder::class); //done - one country

        //zone
        $this->call(ZoneSiteTableSeeder::class); //done

        //langs
        $this->call(LangSiteTableSeeder::class); //done

        //tenants
        $this->call(TenantSiteTableSeeder::class); //done

        //hooks
        $this->call(HookSiteTableSeeder::class); //done

        //Cms permissions
        $this->call(CmsPermissionsTableSeeder::class);

        //Festival
        $this->call(FestivalsTableSeeder::class);

        //Logs
        //$this->call(LogsTableSeeder::class);

        //Medias
        $this->call(MediasTableSeeder::class);

        //Microsites
        $this->call(MicrositesTableSeeder::class);

        //Page
        $this->call(PageTableSeeder::class);

        //StaticModuleContent
        $this->call(StaticModuleContentTableSeeder::class);

        //Installation check
        $this->call(SitePropTableSeeder::class);

    }
}
