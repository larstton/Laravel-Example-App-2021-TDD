<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('host_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('host_id')->index();
            $table->uuid('team_id')->index();
            $table->uuid('user_id')->index();
            $table->string('name')->nullable();
            $table->boolean('paid')->default(false)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
