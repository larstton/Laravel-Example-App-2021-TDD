<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeWebCheckIndexUniqueOnWebChecksTable extends Migration
{
    public function up()
    {
        Schema::table('web_checks', function (Blueprint $table) {
            $table->dropIndex(['host_id', 'path', 'method', 'port', 'headers_md5_sum']);
            $table->unique(['host_id', 'path', 'method', 'port', 'headers_md5_sum']);
        });
    }
}
