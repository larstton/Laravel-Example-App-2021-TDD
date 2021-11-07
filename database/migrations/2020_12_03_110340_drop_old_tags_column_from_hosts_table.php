<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOldTagsColumnFromHostsTable extends Migration
{
    public function up()
    {
        Schema::table('hosts', function (Blueprint $table) {
            $table->dropColumn('tags_v2');
        });
    }
}
