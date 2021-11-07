<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->uuid('recipient_id')->index();
            $table->uuid('event_id')->index();
            $table->integer('reminders_count')->default(0);
            $table->timestamp('last_reminder_created_at')->nullable();
        });
    }
}
