<?php

namespace MarghoobSuleman\HashtagCms\Console\Commands;

use MarghoobSuleman\HashtagCms\Models\SiteProp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\StreamOutput;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

class CmsInstall extends Command
{

    protected $composer;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Hashtag CMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            if(DB::connection()->getDatabaseName()) {
                $hasTable = Schema::hasTable('migrations');
                $hasPropsTable = Schema::hasTable('site_props');

                if(!$hasTable || !$hasPropsTable) {

                    $this->installNow("migrate");

                    $this->info("> Generating key");
                    Artisan::call("key:generate");

                    $this->info("> Generating passport key");
                    Artisan::call("passport:keys");

                    $this->info("> Initiating passport install");
                    Artisan::call("passport:install");


                    //export some js too
                    Artisan::call("vendor:publish", [
                        '--tag'=>'hashtagcms.assets'
                    ]);

                    Artisan::call("vendor:publish", [
                        '--tag'=>'hashtagcms.views.frontend'
                    ]);

                    $this->showInstallationInfo();

                } else if($hasPropsTable) {

                    $installInfo = SiteProp::where("name", "site_installed")->first();
                    if($installInfo == null) {
                        $this->info("Tables are empty!");
                        $this->askQuestionAndInstall("Would you like to to try again? [Y/N]");
                    } else if($installInfo && $installInfo->value == 1) {

                        $this->askQuestionAndInstall("Looks like site is already configured. Would you like to fresh install? [Y/N]");

                    } else {
                        //If it's been in database
                        $this->info("");
                        $this->error("It's already installed.");
                        $this->showInstallationInfo();
                    }
                }
            } else {
                $this->error("Got some error.");
            }
        } catch (\Exception $exception) {
            $this->error("\nUnable to connect database. Did you change the .env file?\n");

            $this->error($exception->getMessage());
        }
    }

    private function askQuestionAndInstall($question) {
        $fresh = $this->ask($question);
        $fresh = (strtolower($fresh) == "y" || strtolower($fresh) == "yes") ? "yes" : "no";

        if($fresh=="yes") {
            $this->installNow("migrate:fresh");
            $this->showInstallationInfo();
        } else {
            $this->error("Installation cancelled.");
        }
    }

    private function installNow($installation) {
        $this->alert("Installing HashtagCMS. Please wait...");

        $this->info("> Creating Tables...");

        Artisan::call($installation);

        $this->info("> Seeding Tables...");

        Artisan::call("db:seed", [
            '--class'=>'MarghoobSuleman\\HashtagCms\\Database\\Seeds\\HashtagCmsDatabaseSeeder'
        ]);

    }




    private function showInstallationInfo() {

        $siteInstall = env("APP_URL")."/install";
        $this->alert("Please visit $siteInstall to configure your site.");
    }
}
