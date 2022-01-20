<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyHooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //@todo: will remove in 1.4.0
        Schema::table('hooks', function (Blueprint $table) {

            if (!Schema::hasColumn('hooks', 'direction')) {
                $table->enum('direction', ['', 'vertical', 'horizotnal'])->nullable()->default('');
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
