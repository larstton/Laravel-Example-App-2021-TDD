<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('team_settings', function (Blueprint $table) {
            $table->uuid('team_id')->primary();
            $table->json('value');
            $table->timestamps();
        });
    }
}
