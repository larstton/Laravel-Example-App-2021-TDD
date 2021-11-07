<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateImageColumnStatusPages extends Migration
{
    public function up()
    {
        Schema::table('status_pages', function (Blueprint $table) {
            $table->dropColumn('image');
        });
        DB::statement("ALTER TABLE status_pages ADD COLUMN image MEDIUMBLOB DEFAULT NULL AFTER meta");
    }
}
