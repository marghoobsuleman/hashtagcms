<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //@todo: this  will be removed in 1.4.0 release.
        Schema::table('categories', function (Blueprint $table) {

            if (!Schema::hasColumn('categories', 'controller_name')) {
                $table->string('controller_name', 255)->nullable();
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
