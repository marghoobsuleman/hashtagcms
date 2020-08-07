<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitePropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_props', function (Blueprint $table) {

            $table->id();
            $table->bigInteger("site_id", false, true);
            $table->bigInteger("tenant_id", false, true);
            $table->string("name", 100);
            $table->text("value");
            $table->string("group_name", 60)->nullable();
            $table->tinyInteger("is_public")->nullable()->default(0);

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
        Schema::dropIfExists('site_props');
    }
}
