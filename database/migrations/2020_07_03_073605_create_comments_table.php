<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("parent_id")->nullable();
            $table->bigInteger("site_id");
            $table->string("name", 255);
            $table->string("email", 255);
            $table->bigInteger("category_id");
            $table->bigInteger("page_id")->nullable();
            $table->bigInteger("user_id")->nullable();
            $table->text("comment");
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
        Schema::dropIfExists('comments');
    }
}
