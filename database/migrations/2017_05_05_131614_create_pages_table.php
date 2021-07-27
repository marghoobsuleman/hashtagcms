<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('parent_id', false, true)->nullable();
            $table->bigInteger('site_id', false, true);
            $table->bigInteger('microsite_id')->nullable()->default(0);
            $table->bigInteger('tenant_id', false, true)->nullable();
            $table->bigInteger('category_id', false, true)->nullable();

            $table->string('alias', 60)->nullable();

            $table->tinyInteger('exclude_in_listing')->nullable()->default(0);

            $table->enum('content_type', ['page', 'blog'])->default('page');

            $table->tinyInteger('position')->nullable()->default(0);

            $table->string('link_rewrite', 255);
            $table->string('link_navigation', 255)->nullable()->comment('If you want to display on href. you must have apache rule for this if this is different from link_rewrite');
            $table->string('menu_placement', 100)->nullable()->default("bottom");


            $table->text('header_content')->nullable();
            $table->text('footer_content')->nullable();

            $table->bigInteger('insert_by', false, true);
            $table->bigInteger('update_by', false, true)->nullable();

            $table->tinyInteger('enable_comments')->nullable()->default(0);
            $table->tinyInteger('required_login')->nullable()->default(0);
            $table->tinyInteger('publish_status')->nullable()->default(0);
            $table->bigInteger('read_count')->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

        });

        //Language
        Schema::create('page_langs', function (Blueprint $table) {

            $table->bigInteger('page_id', false, true);
            $table->bigInteger('lang_id', false, true);

            $table->string('name', 128);
            $table->string('title', 128);

            $table->text('description')->nullable();
            $table->text('page_content');

            $table->enum('link_relation', ['alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag'])->nullable();
            $table->enum('target', ['_blank','_parent','_self','_top'])->nullable();

            $table->string('active_key', 128)->nullable();

            $table->string('meta_title', 160)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_robots', 255)->nullable();
            $table->string('meta_canonical', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        //Relation
        Schema::table("page_langs", function(Blueprint $table) {

            $table->foreign('page_id')
                ->references('id')
                ->on('pages')
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

        Schema::dropIfExists('pages');
        Schema::dropIfExists('page_langs');

        Schema::enableForeignKeyConstraints();

    }
}
