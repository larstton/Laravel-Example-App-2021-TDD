<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitToRulesTable extends Migration
{
    public function up()
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->string('unit', 3)->nullable()->default(null)->after('threshold');
        });
    }
}
