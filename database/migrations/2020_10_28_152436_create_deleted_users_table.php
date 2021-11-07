<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeletedUsersTable extends Migration
{
    public function up()
    {
        Schema::create('deleted_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->boolean('product_news')->default(true);
            $table->timestamps();
        });
    }
}
