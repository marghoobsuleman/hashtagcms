<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // @todo: will remove in 1.4.0
        Schema::table('modules', function($table)
        {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE modules CHANGE COLUMN data_type data_type VARCHAR(100) NOT NULL;");
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
