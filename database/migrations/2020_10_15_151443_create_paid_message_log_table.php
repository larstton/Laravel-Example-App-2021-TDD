<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaidMessageLogTable extends Migration
{
    public function up()
    {
        Schema::create('paid_message_log', function (Blueprint $table) {
            $table->uuid('recipient_id')->index();
            $table->uuid('team_id')->index();
            $table->enum('media_type', [
                'email', 'slack', 'sms', 'pushover',
                'telegram', 'webhook', 'whatsapp', 'phonecall', 'msteams', 'integromat',
            ])->nullable();
            $table->string('sendto', 200);
            $table->timestamp('created_at')->nullable();
        });
    }
}
