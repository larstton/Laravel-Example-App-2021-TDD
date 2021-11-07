<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('event_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_id')->index();
            $table->uuid('team_id')->index();
            $table->uuid('user_id');
            $table->string('nickname', 100)->nullable();
            $table->text('text');
            $table->unsignedTinyInteger('visible_to_guests')
                ->default(0)
                ->comment('Is the comment visible for guests');
            $table->unsignedTinyInteger('statuspage')
                ->default(0)
                ->comment('Publish comment on status pages');
            $table->unsignedTinyInteger('forward')
                ->default(0)
                ->comment('Forward comment to subscribed email recipients.');

            $table->timestamp('created_at')->nullable();

            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });
    }
}
