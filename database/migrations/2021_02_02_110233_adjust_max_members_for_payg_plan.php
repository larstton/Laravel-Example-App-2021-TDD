<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdjustMaxMembersForPaygPlan extends Migration
{
    public function up()
    {
        DB::statement("UPDATE `teams` SET `max_members` = 99 WHERE `plan`='payg'");
    }
}
