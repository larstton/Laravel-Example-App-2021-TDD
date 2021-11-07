<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAgentDataTable extends Migration
{
    public function up()
    {
        Schema::create('user_agent_data', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->index();
            $table->uuid('team_id')->index();
            $table->json('data');
            $table->timestamp('created_at')->nullable();
        });
    }
}
