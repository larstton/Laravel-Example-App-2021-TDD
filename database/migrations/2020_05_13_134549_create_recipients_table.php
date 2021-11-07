<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id')->index();
            $table->uuid('user_id')->index();

            // status
            $table->boolean('verified')->default(0);
            $table->boolean('active')->default(1);
            $table->string('verification_token', 8)->nullable();
            $table->integer('permanent_failures_last_24_h')->default(0)->nullable();
            $table->boolean('administratively_disabled')->default(0);

            // sending settings
            $table->enum('media_type', [
                'email', 'slack', 'sms', 'pushover',
                'telegram', 'webhook', 'whatsapp', 'phonecall', 'msteams', 'integromat',
            ])->nullable();
            $table->string('sendto', 200);
            $table->string('description', 100)->nullable();
            $table->string('option1', 100)->nullable();
            $table->integer('reminder_delay')->default(600);
            $table->integer('maximum_reminders')->default(3);

            // content settings
            $table->boolean('reminders')->default(0);
            $table->boolean('daily_reports')->default(0);
            $table->boolean('monthly_reports')->default(0);
            $table->boolean('daily_summary')->default(0);
            $table->boolean('weekly_reports')->default(0);
            $table->boolean('comments')->default(0);
            $table->boolean('alerts')->default(0);
            $table->boolean('warnings')->default(0);
            $table->boolean('event_uuids')->default(0);
            $table->boolean('recoveries')->default(0);
            $table->json('rules')->nullable();
            $table->json('extra_data')->nullable();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipients');
    }
}
