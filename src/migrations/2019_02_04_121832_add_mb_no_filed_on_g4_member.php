<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMbNoFiledOnG4Member extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("g4_member", function(Blueprint $table){
            $table->increments('mb_no');
            $table->string('mb_remember')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("g4_member", function(Blueprint $table){
            $table->dropColumn('mb_no');
            $table->dropColumn("mb_remember");
        });
    }
}
