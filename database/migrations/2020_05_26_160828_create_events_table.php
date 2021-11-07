<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id')->index();
            $table->uuid('host_id')->index()->nullable();
            $table->uuid('rule_id');
            $table->uuid('check_id')->index();
            $table->string('check_key');
            $table->enum('action', ['warn', 'alert', 'snooze', 'ignore'])->index();
            $table->unsignedTinyInteger('state')->default(1)->index();
            $table->unsignedTinyInteger('reminders')->default(1);
            $table->schemalessAttributes('meta');
            $table->uuid('affected_host_id')
                ->virtualAs('JSON_UNQUOTE(meta->"$.affectedHost.uuid")')
                ->nullable()->index();
            $table->boolean('is_agent_event')
                ->virtualAs('`host_id` = `check_id`')
                ->index();
            $table->text('last_check_value')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index(['team_id', 'created_at', 'resolved_at']);
        });
    }
}
