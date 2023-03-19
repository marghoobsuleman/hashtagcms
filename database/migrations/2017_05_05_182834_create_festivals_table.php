<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     *
     */
    public function up()
    {
        Schema::create('festivals', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('site_id', false, true);

            $table->string('image', 255)->nullable();
            $table->string('body_css', 255)->nullable();
            $table->longText('lottie')->nullable();
            $table->string('header_css', 255)->nullable();
            $table->string('footer_css', 255)->nullable();

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->tinyInteger('publish_status')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();

        });

        //Relation on site
        Schema::table('festivals', function (Blueprint $table) {

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

        Schema::dropIfExists('festivals');

        Schema::enableForeignKeyConstraints();

    }
};
