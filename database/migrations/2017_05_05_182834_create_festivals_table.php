<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFestivalsTable extends Migration
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
            $table->string('header_css', 255)->nullable();
            $table->string('footer_css', 255)->nullable();

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->tinyInteger('publish_status')->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

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
}
