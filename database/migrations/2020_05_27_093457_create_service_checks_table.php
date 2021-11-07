<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceChecksTable extends Migration
{
    public function up()
    {
        Schema::create('service_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('host_id')->index();
            $table->uuid('user_id')->index();
            $table->unsignedTinyInteger('active')->default(0)->index();
            $table->unsignedInteger('check_interval')->default(60);
            $table->enum('protocol', ['tcp', 'udp', 'icmp', 'ssl']);
            $table->enum('service', [
                'http', 'https', 'ssh', 'imap', 'imaps',
                'pop3', 'smtp', 'smtps', 'pop3s',
                'tcp', 'ping', 'sip', 'iax2',
            ]);
            $table->unsignedInteger('port')->default(0);

            $table->unsignedTinyInteger('in_progress')->default(0)->index();
            $table->unsignedTinyInteger('last_success')->nullable();
            $table->text('last_message')->nullable();

            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->index(['host_id', 'protocol', 'service', 'port']);
        });
    }
}
