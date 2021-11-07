<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlanLastChangedAtAndPreviousPlanToTeamsTable extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('previous_plan')->nullable()->after('plan');
            $table->timestamp('plan_last_changed_at')->nullable()->after('has_granted_access_to_support');
        });
    }
}
