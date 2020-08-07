<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {

            $table->id();
            $table->bigInteger("user_id", false, true);
            $table->string("father_name", 255)->nullable();
            $table->string("mother_name", 255)->nullable();
            $table->enum("id_card_type", ['Aadhaar', 'Passport', 'Driving License'])->nullable()->default("Aadhaar");
            $table->string("id_card_number", 50)->nullable();

            $table->string("mobile", 50);
            $table->date("date_of_birth")->nullable();
            $table->string("gender", 20)->nullable();

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
        Schema::dropIfExists('user_profiles');
    }
}
