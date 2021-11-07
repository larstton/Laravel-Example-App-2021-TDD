<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamStatisticsTable extends Migration
{
    public function up()
    {
        Schema::create('team_statistics', function (Blueprint $table) {
            $table->uuid('team_id')->index();
            $table->string('key', 50);
            $table->integer('value');
            $table->timestamp('last_summary_at')->nullable();
            $table->integer('last_summary_total');
        });
    }
}
