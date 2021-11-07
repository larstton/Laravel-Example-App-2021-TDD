<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCagentTrackerTable extends Migration
{
    public function up()
    {
        Schema::create('cagent_tracker', function (Blueprint $table) {
            $table->uuid('host_id');
            $table->char('host_hash', 32);
            $table->json('host_info');
            $table->bigInteger('count');
            $table->timestamp('next_reminder_at')->nullable();
            $table->timestamps();
            $table->unique(['host_id', 'host_hash']);
        });
    }
}
