<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeWrongDateDefaultAtMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("g4_member", function(Blueprint $table) {
            $table->dateTime('mb_nick_date')->nullable()->default(null)->change();
            $table->dateTime('mb_today_login')->nullable()->default(null)->change();
            $table->dateTime('mb_datetime')->nullable()->default(null)->change();
            $table->dateTime('mb_email_certify')->nullable()->default(null)->change();
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
