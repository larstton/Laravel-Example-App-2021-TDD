<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnmpChecksTable extends Migration
{
    public function up()
    {
        Schema::create('snmp_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('host_id')->index();
            $table->uuid('user_id')->index();
            $table->unsignedTinyInteger('active')->default(1)->index();
            $table->unsignedInteger('check_interval')->default(90);
            $table->string('preset', 100);
            $table->unsignedTinyInteger('last_success')->nullable();
            $table->text('last_message')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });
    }
}
