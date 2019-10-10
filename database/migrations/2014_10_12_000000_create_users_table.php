<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name'); //displayName
            $table->string('username')->unique(); //username
//            $table->string('email')->unique(); //mail
            $table->string('email'); //mail
            $table->string('password');
            $table->string('rol')->nullable();
            $table->string('department'); //department
            $table->string('office'); //physicalDeliveryOfficeName
            $table->string('detail')->nullable(); //Detalles del usuario
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
