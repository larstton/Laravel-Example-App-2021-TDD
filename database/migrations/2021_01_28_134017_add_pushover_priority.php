<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPushoverPriority extends Migration
{
    public function up()
    {
        DB::statement("UPDATE `recipients` SET `extra_data` = '{\"priority\":1}' WHERE `media_type`='pushover'");
    }
}
