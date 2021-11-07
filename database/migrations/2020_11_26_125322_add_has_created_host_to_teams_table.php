<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasCreatedHostToTeamsTable extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('has_created_host')->default(true)->after('onboarded');
        });
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('has_created_host')->default(false)->change();
        });
    }
}
