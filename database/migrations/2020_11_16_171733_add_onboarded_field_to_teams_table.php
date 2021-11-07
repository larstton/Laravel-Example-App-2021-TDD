<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnboardedFieldToTeamsTable extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('onboarded')->default(true)->after('has_granted_access_to_support');
        });
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('onboarded')->default(false)->change();
        });
    }
}
