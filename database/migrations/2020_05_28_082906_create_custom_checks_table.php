<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomChecksTable extends Migration
{
    public function up()
    {
        Schema::create('custom_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('host_id')->index();
            $table->uuid('user_id')->index();
            $table->string('name', 25)->index();
            $table->string('token', 12)->unique();
            $table->unsignedInteger('expected_update_interval')->nullable();
            $table->text('last_influx_error')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->unique(['host_id', 'name']);
        });
    }
}
