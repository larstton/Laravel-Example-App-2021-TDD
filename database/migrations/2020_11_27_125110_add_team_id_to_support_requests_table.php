<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdToSupportRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('support_requests', function (Blueprint $table) {
            $table->uuid('team_id')->index()->nullable()->after('user_id');
        });
    }
}
