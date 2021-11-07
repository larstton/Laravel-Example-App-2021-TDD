<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeHeaderMd5NullableWebChecks extends Migration
{
    public function up()
    {
        Schema::table('web_checks', function (Blueprint $table) {
            $table->string('headers_md5_sum', 32)->nullable()->change();
        });
    }
}
