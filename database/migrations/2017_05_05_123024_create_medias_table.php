<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     */
    public function up()
    {
        Schema::create('medias', function(Blueprint $table) {

            $table->id();
            $table->bigInteger('site_id', false, true);

            $table->bigInteger('user_id', false, true)->nullable();

            $table->bigInteger('content_id', false, true)->nullable();
            $table->string('content_for', 255)->nullable();
            $table->string('normal', 255)->nullable();

            $table->string('extra_extra_extra_high', 255)->nullable();
            $table->string('extra_extra_high', 255)->nullable();
            $table->string('extra_high', 255)->nullable();
            $table->string('high', 255)->nullable();
            $table->string('medium', 255)->nullable();
            $table->string('low', 255)->nullable();

            $table->string('image_type', 255)->nullable()->default('content');
            $table->string('image_group')->nullable();
            $table->string('image_key', 100)->nullable();
            $table->integer('position')->nullable();

            $table->timestamps();
            $table->softDeletes();

        });

        Schema::create('media_langs', function (Blueprint $table) {
            $table->bigInteger('media_id', false, true);
            $table->bigInteger('lang_id', false, true);
            $table->string('title', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        //Relation
        Schema::table("media_langs", function (Blueprint $table) {
            $table->foreign("media_id")
                ->references("id")
                ->on("medias")
                ->onDelete('cascade');

            $table->primary(['media_id', 'lang_id']);

        });

        //Relation on site
        Schema::table('medias', function (Blueprint $table) {

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

        Schema::dropIfExists('medias');
        Schema::dropIfExists('media_langs');

        Schema::enableForeignKeyConstraints();

    }
}
