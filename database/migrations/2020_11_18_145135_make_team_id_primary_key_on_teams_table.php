<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeTeamIdPrimaryKeyOnTeamsTable extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex('teams_id_index');
            $table->uuid('id')->primary()->change();
        });
    }
}
