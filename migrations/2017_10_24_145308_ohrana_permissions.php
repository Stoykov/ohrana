<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OhranaPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ohrana_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned()->index()->foreign()->references("id")->on("ohrana_roles")->onDelete("cascade");
            $table->string('rule')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ohrana_permissions');
    }
}
