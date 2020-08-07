<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */


    public function up()
    {

        Schema::create('sites', function(Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->bigInteger('category_id',false, true)->nullable()->comment('This is default category');
            $table->bigInteger('theme_id',false, true)->nullable()->comment('This is default theme');
            $table->bigInteger('tenant_id',false, true)->nullable()->comment('This is default tenent');
            $table->bigInteger('lang_id',false, true)->nullable()->default(1)->comment('This is default language');
            $table->bigInteger('country_id', false, true)->nullable()->default(1)->comment('This is default language');
            $table->tinyInteger('under_maintenance')->nullable()->default(0);
            $table->string('domain', 255);
            $table->string('context', 40);
            $table->string('favicon', 255)->nullable();
            $table->integer('lang_count')->nullable()->defualt(1);
            $table->timestamps();
            $table->softDeletes();

        });

        //Langs
        Schema::create('site_langs', function (Blueprint $table) {

            $table->bigInteger('site_id', false, true);
            $table->bigInteger('lang_id', false, true);
            $table->string('title', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        //Relation
        Schema::table('site_langs', function (Blueprint $table) {

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('sites');
        Schema::dropIfExists('site_langs');

        Schema::enableForeignKeyConstraints();


    }
}
